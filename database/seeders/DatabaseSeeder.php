<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Employer;
use App\Models\Category;
use App\Models\Location;
use App\Models\Job;
use App\Models\Wallet;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\JobSeeker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Delete existing data to prevent duplicates on re-run (Cascade delete will handle most)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Category::truncate();
        Location::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $employerNames = ['Budi Setiawan', 'Siti Aminah', 'Andi Kusuma', 'Reza Pahlevi', 'Dewi Lestari'];
        $employers = [];

        foreach ($employerNames as $index => $name) {
            $userId = Str::uuid();
            $username = strtolower(explode(' ', $name)[0]) . rand(10, 99);
            
            $user = User::create([
                'userID' => $userId,
                'username' => $username,
                'email' => $username . '@example.com',
                'password' => Hash::make('password123'),
                'fullName' => $name,
                'phone' => '0812' . rand(10000000, 99999999),
                'role' => 'EMPLOYER',
            ]);
            
            Employer::create([
                'employerID' => $userId,
                'displayName' => $name,
                'address' => 'Jl. Merdeka No. ' . rand(1, 100)
            ]);

            Wallet::create([
                'walletID' => Str::uuid(),
                'userID' => $userId,
                'balance' => rand(10, 50) * 100000
            ]);

            $employers[] = $userId;
        }

        // Create Job Seeker User for simulation
        $seekerUserId = Str::uuid();
        User::create([
            'userID' => $seekerUserId,
            'username' => 'seeker_dummy',
            'email' => 'seeker@example.com',
            'password' => Hash::make('password123'),
            'fullName' => 'Ahmad Fikri',
            'phone' => '081298765432',
            'role' => 'JOB_SEEKER',
        ]);

        JobSeeker::create([
            'jobSeekerID' => $seekerUserId,
        ]);

        $seekerWallet = Wallet::create([
            'walletID' => Str::uuid(),
            'userID' => $seekerUserId,
            'balance' => 0
        ]);

        // Create Categories
        $catKreatifId = Str::uuid();
        Category::create(['categoryID' => $catKreatifId, 'name' => 'Kreatif & Desain', 'description' => 'Pekerjaan terkait desain grafis dan konten.']);

        $catDigitalId = Str::uuid();
        Category::create(['categoryID' => $catDigitalId, 'name' => 'Digital & IT', 'description' => 'Pekerjaan terkait pemrograman dan digital marketing.']);

        $catFisikId = Str::uuid();
        Category::create(['categoryID' => $catFisikId, 'name' => 'Jasa Fisik & Harian', 'description' => 'Pekerjaan kasar, perbaikan, dan bantuan fisik.']);

        // Create Locations
        $locations = [];
        $locData = [
            ['addressLine' => 'Jl. Thamrin', 'city' => 'Jakarta Pusat', 'province' => 'DKI Jakarta', 'postalCode' => '10310'],
            ['addressLine' => 'Jl. Braga', 'city' => 'Bandung', 'province' => 'Jawa Barat', 'postalCode' => '40111'],
            ['addressLine' => 'Jl. Malioboro', 'city' => 'Yogyakarta', 'province' => 'DI Yogyakarta', 'postalCode' => '55271'],
            ['addressLine' => 'Jl. Pemuda', 'city' => 'Surabaya', 'province' => 'Jawa Timur', 'postalCode' => '60271']
        ];

        foreach ($locData as $loc) {
            $locId = Str::uuid();
            Location::create(array_merge(['locationID' => $locId], $loc));
            $locations[] = $locId;
        }

        // Create Jobs
        $jobsData = [
            // Physical Jobs (is_remote: false)
            [
                'categoryID' => $catFisikId,
                'title' => 'Cat Pagar Rumah 50 Meter',
                'description' => 'Membutuhkan tenaga untuk mengecat pagar rumah seharian. Alat dan bahan sudah disediakan, makan siang ditanggung.',
                'payAmount' => 250000,
                'is_remote' => false,
                'image_url' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catFisikId,
                'title' => 'Bantu Pindahan Kos',
                'description' => 'Butuh 2 orang tenaga pria untuk membantu angkat barang pindahan kos (lemari kecil, kasur, kotak-kotak). Jarak dekat.',
                'payAmount' => 150000,
                'is_remote' => false,
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catFisikId,
                'title' => 'Bersihkan Halaman Belakang',
                'description' => 'Tolong bersihkan daun kering dan potong rumput liar di halaman belakang seluas 10x10 meter. Cukup bawa tenaga, sapu lidi ada.',
                'payAmount' => 100000,
                'is_remote' => false,
                'image_url' => 'https://images.unsplash.com/photo-1558904541-efa843a96f09?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catFisikId,
                'title' => 'Perbaiki Pipa Bocor Ringan',
                'description' => 'Ada pipa wastafel yang sedikit bocor di bagian sambungan. Butuh orang yang bisa ganti seal/selotip pipa.',
                'payAmount' => 80000,
                'is_remote' => false,
                'image_url' => 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catFisikId,
                'title' => 'Cuci 2 Mobil di Rumah',
                'description' => 'Saya tidak ada waktu mencuci mobil. Tolong cucikan 2 mobil MPV di carport rumah saya pagi ini.',
                'payAmount' => 120000,
                'is_remote' => false,
                'image_url' => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            
            // Digital Jobs (is_remote: true)
            [
                'categoryID' => $catKreatifId,
                'title' => 'Desain Logo Kedai Kopi',
                'description' => 'Saya butuh logo simple untuk kedai kopi kecil-kecilan bernama "Kopi Senja". Tema minimalis dan warna hangat.',
                'payAmount' => 350000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1626808642875-0aa54525ce51?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catKreatifId,
                'title' => 'Edit Video Reels IG (Durasi 30 Detik)',
                'description' => 'Saya punya video mentah liburan, tolong editkan jadi 1 Reels IG yang aesthetic dengan musik yang sedang tren.',
                'payAmount' => 200000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catDigitalId,
                'title' => 'Admin Sosmed (Balas Chat) 1 Hari',
                'description' => 'Toko online saya sedang ramai promo, butuh bantuan membalas chat pelanggan di Shopee/Tokopedia untuk shift malam ini saja.',
                'payAmount' => 150000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1553890666-88ab1a123eb4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catKreatifId,
                'title' => 'Buat Konten Presentasi Canva (10 Slide)',
                'description' => 'Materi teks sudah ada. Saya butuh orang yang bisa menyusunnya ke dalam 10 slide presentasi Canva yang menarik untuk tugas kampus.',
                'payAmount' => 100000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catDigitalId,
                'title' => 'Bantu Input Data Excel (200 Baris)',
                'description' => 'Saya punya foto nota-nota tulisan tangan, tolong rekap dan input nominalnya ke dalam tabel Excel. Pekerjaan santai.',
                'payAmount' => 120000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ],
            [
                'categoryID' => $catDigitalId,
                'title' => 'Perbaiki Bug Error di Website WordPress',
                'description' => 'Website toko online WordPress saya ada error "White Screen of Death". Butuh yang paham cPanel & PHP untuk bantu cek log error.',
                'payAmount' => 400000,
                'is_remote' => true,
                'image_url' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            ]
        ];

        $firstJobId = null;

        foreach ($jobsData as $index => $job) {
            $jobId = Str::uuid();
            if ($index === 0) $firstJobId = $jobId;

            Job::create([
                'jobID' => $jobId,
                'employerID' => $employers[array_rand($employers)],
                'categoryID' => $job['categoryID'],
                'locationID' => $job['is_remote'] ? null : $locations[array_rand($locations)],
                'title' => $job['title'],
                'description' => $job['description'],
                'payAmount' => $job['payAmount'],
                'is_remote' => $job['is_remote'],
                'image_url' => $job['image_url'],
                'jobStatus' => 'OPEN'
            ]);
        }

        // Create a Contract & Escrow Payment for the Job Seeker (Simulation)
        $contractId = Str::uuid();
        Contract::create([
            'contractID' => $contractId,
            'jobID' => $firstJobId,
            'employerID' => $employers[0], // Arbitrary
            'jobSeekerID' => $seekerUserId,
            'status' => 'ACTIVE'
        ]);

        Payment::create([
            'paymentID' => Str::uuid(),
            'walletID' => $seekerWallet->walletID,
            'contractID' => $contractId,
            'amount' => 250000,
            'status' => 'HELD' // Escrow
        ]);
    }
}
