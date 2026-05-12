<?php
namespace App\Http\Controllers;
use App\Models\Job;
use Illuminate\Http\Request;

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
        return view('home', compact('jobs'));
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
            'image_url' => 'nullable|url',
            'is_remote' => 'nullable|boolean',
            'addressLine' => 'required_unless:is_remote,1',
            'city' => 'required_unless:is_remote,1',
            'province' => 'required_unless:is_remote,1',
        ]);

        $employerId = auth()->user()->userID; // EmployerID is same as UserID
        $locationId = null;

        $isRemote = $request->has('is_remote') && $request->is_remote;

        if (!$isRemote) {
            $locationId = \Illuminate\Support\Str::uuid();
            \App\Models\Location::create([
                'locationID' => $locationId,
                'addressLine' => $request->addressLine,
                'city' => $request->city,
                'province' => $request->province,
                'postalCode' => $request->postalCode ?? '00000'
            ]);
        }

        \App\Models\Job::create([
            'jobID' => \Illuminate\Support\Str::uuid(),
            'employerID' => $employerId,
            'categoryID' => $request->categoryID,
            'locationID' => $locationId,
            'title' => $request->title,
            'description' => $request->description,
            'payAmount' => $request->payAmount,
            'jobStatus' => 'OPEN',
            'is_remote' => $isRemote,
            'image_url' => $request->image_url,
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
            'image_url' => 'nullable|url',
            'is_remote' => 'nullable|boolean',
            'addressLine' => 'required_unless:is_remote,1',
            'city' => 'required_unless:is_remote,1',
            'province' => 'required_unless:is_remote,1',
        ]);

        $isRemote = $request->has('is_remote') && $request->is_remote;

        if (!$isRemote) {
            if ($job->locationID) {
                // Update existing location
                $job->location->update([
                    'addressLine' => $request->addressLine,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postalCode' => $request->postalCode ?? '00000'
                ]);
            } else {
                // Create new location
                $locationId = \Illuminate\Support\Str::uuid();
                \App\Models\Location::create([
                    'locationID' => $locationId,
                    'addressLine' => $request->addressLine,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postalCode' => $request->postalCode ?? '00000'
                ]);
                $job->locationID = $locationId;
            }
        }

        $job->update([
            'title' => $request->title,
            'categoryID' => $request->categoryID,
            'description' => $request->description,
            'payAmount' => $request->payAmount,
            'is_remote' => $isRemote,
            'image_url' => $request->image_url,
        ]);

        return redirect()->route('home')->with('success', 'Lowongan kerja berhasil diperbarui!');
    }

    public function destroy($id) {
        $job = Job::findOrFail($id);
        if (auth()->user()->role !== 'EMPLOYER' || $job->employerID !== auth()->user()->userID) return redirect()->route('home');
        
        \App\Models\Application::where('jobID', $id)->delete();
        $job->delete();
        return redirect()->route('home')->with('success', 'Lowongan kerja berhasil dihapus!');
    }
}