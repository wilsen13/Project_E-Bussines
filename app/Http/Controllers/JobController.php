<?php
namespace App\Http\Controllers;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index(Request $request) {
        $query = Job::with(['employer.user', 'location', 'category'])->where('jobStatus', 'OPEN');
        
        if ($request->has('q') && $request->q != '') {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function($catQuery) use ($searchTerm) {
                      $catQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        
        $jobs = $query->latest('createdAt')->get();

        // Fetch active contracts for the logged-in user
        $activeContracts = collect();
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'JOB_SEEKER') {
                $activeContracts = \App\Models\Contract::with(['job.employer', 'payment'])
                    ->where('jobSeekerID', $user->userID)
                    ->whereIn('status', ['ACTIVE', 'WAITING_REVIEW'])
                    ->latest('startAt')
                    ->get();
            } elseif ($user->role === 'EMPLOYER') {
                $activeContracts = \App\Models\Contract::with(['job', 'jobSeeker.user', 'payment'])
                    ->where('employerID', $user->userID)
                    ->whereIn('status', ['ACTIVE', 'WAITING_REVIEW'])
                    ->latest('startAt')
                    ->get();
            }
        }

        return view('home', compact('jobs', 'activeContracts'));
    }

    public function create() {
        if (auth()->user()->role !== 'EMPLOYER') return redirect()->route('home');
        $categories = \App\Models\Category::all();
        return view('jobs.create', compact('categories'));
    }

    public function store(Request $request) {
        if (auth()->user()->role !== 'EMPLOYER') return redirect()->route('home');

        $request->validate([
            'title' => 'required|string|max:255',
            'categoryID' => 'required|exists:categories,categoryID',
            'description' => 'required|string',
            'payAmount' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_remote' => 'nullable|boolean',
            'addressLine' => 'required_unless:is_remote,1',
            'city' => 'required_unless:is_remote,1',
            'province' => 'required_unless:is_remote,1',
        ]);

        $employerId = auth()->user()->userID;
        $locationId = null;

        $isRemote = $request->has('is_remote') && $request->is_remote;

        // Create Location FIRST, then use its persisted primary key
        if (!$isRemote) {
            $location = \App\Models\Location::create([
                'addressLine' => $request->addressLine,
                'city' => $request->city,
                'province' => $request->province,
                'postalCode' => $request->postalCode ?? '00000'
            ]);
            $locationId = $location->locationID; // Get the auto-generated UUID
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('jobs', 'public');
        }

        // Create Job — HasUuids auto-generates jobID
        \App\Models\Job::create([
            'employerID' => $employerId,
            'categoryID' => $request->categoryID,
            'locationID' => $locationId,
            'title' => $request->title,
            'description' => $request->description,
            'payAmount' => $request->payAmount,
            'jobStatus' => 'OPEN',
            'is_remote' => $isRemote,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('home')->with('success', 'Lowongan kerja berhasil dibuat!');
    }

    public function show($id) {
        $job = Job::with(['employer.user', 'location', 'category'])->findOrFail($id);
        return view('jobs.show', compact('job'));
    }

    public function address($id) {
        $job = Job::with(['location'])->findOrFail($id);
        return view('jobs.address', compact('job'));
    }

    public function edit($id) {
        $job = Job::findOrFail($id);
        if (auth()->user()->role !== 'EMPLOYER' || $job->employerID !== auth()->user()->userID) return redirect()->route('home');
        $categories = \App\Models\Category::all();
        return view('jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, $id) {
        $job = Job::findOrFail($id);
        if (auth()->user()->role !== 'EMPLOYER' || $job->employerID !== auth()->user()->userID) return redirect()->route('home');

        $request->validate([
            'title' => 'required|string|max:255',
            'categoryID' => 'required|exists:categories,categoryID',
            'description' => 'required|string',
            'payAmount' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_remote' => 'nullable|boolean',
            'addressLine' => 'required_unless:is_remote,1',
            'city' => 'required_unless:is_remote,1',
            'province' => 'required_unless:is_remote,1',
        ]);

        $isRemote = $request->has('is_remote') && $request->is_remote;

        if (!$isRemote) {
            if ($job->locationID && $job->location) {
                // Update existing location
                $job->location->update([
                    'addressLine' => $request->addressLine,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postalCode' => $request->postalCode ?? '00000'
                ]);
            } else {
                // Create new location — HasUuids auto-generates locationID
                $location = \App\Models\Location::create([
                    'addressLine' => $request->addressLine,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postalCode' => $request->postalCode ?? '00000'
                ]);
                $job->locationID = $location->locationID;
            }
        }

        // Handle image upload
        $imagePath = $job->image_url;
        if ($request->hasFile('image')) {
            // Delete old image if it was a local file
            if ($job->image_url && !str_starts_with($job->image_url, 'http') && Storage::disk('public')->exists($job->image_url)) {
                Storage::disk('public')->delete($job->image_url);
            }
            $imagePath = $request->file('image')->store('jobs', 'public');
        }

        $job->update([
            'title' => $request->title,
            'categoryID' => $request->categoryID,
            'description' => $request->description,
            'payAmount' => $request->payAmount,
            'is_remote' => $isRemote,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('home')->with('success', 'Lowongan kerja berhasil diperbarui!');
    }

    public function destroy($id) {
        $job = Job::findOrFail($id);
        if (auth()->user()->role !== 'EMPLOYER' || $job->employerID !== auth()->user()->userID) return redirect()->route('home');
        
        // Delete associated image file
        if ($job->image_url && !str_starts_with($job->image_url, 'http') && Storage::disk('public')->exists($job->image_url)) {
            Storage::disk('public')->delete($job->image_url);
        }

        \App\Models\Application::where('jobID', $id)->delete();
        $job->delete();
        return redirect()->route('home')->with('success', 'Lowongan kerja berhasil dihapus!');
    }
}