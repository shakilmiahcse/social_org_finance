<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donor;
use App\Models\Fund;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orgId = 2; // "মানবতা যুব সংঘ"
        $adminUserId = 1; // "Shakil Miah"

        // Clean existing data for this organization to avoid duplicate runs bloating the db
        Transaction::where('organization_id', $orgId)->delete();
        Donor::where('organization_id', $orgId)->delete();
        Fund::where('organization_id', $orgId)->delete();

        // 1. Seed Funds
        $fundsData = [
            [
                'name' => 'মানবতা যুব সংঘ মেইন ফান্ড',
                'description' => 'সংগঠনের সাধারণ ও প্রশাসনিক ব্যয় এবং সাধারণ সেবা কার্যক্রম পরিচালনার জন্য মূল ফান্ড।',
                'type' => 'main',
            ],
            [
                'name' => 'সিলেট বন্যাদুর্গতদের ত্রাণ বিতরণ',
                'description' => 'সিলেটের আকস্মিক বন্যায় ক্ষতিগ্রস্ত মানুষের মাঝে শুকনো খাবার, বিশুদ্ধ পানি ও জরুরি ওষুধ বিতরণ কর্মসূচি।',
                'type' => 'campaign',
            ],
            [
                'name' => 'শীতার্তদের কম্বল ও শীতবস্ত্র বিতরণ ২০২৬',
                'description' => 'দেশের উত্তরাঞ্চলের কুড়িগ্রাম ও পঞ্চগড় জেলার শীতার্ত মানুষের মাঝে কম্বল ও শীতবস্ত্র বিতরণ।',
                'type' => 'campaign',
            ],
            [
                'name' => 'ফ্রি ফ্রাইডে মেডিকেল ক্যাম্প',
                'description' => 'প্রত্যন্ত অঞ্চলের সুবিধাবঞ্চিত মানুষের জন্য ফ্রি ব্লাড গ্রুপিং, ডায়াবেটিস পরীক্ষা ও চিকিৎসকের পরামর্শ ক্যাম্প।',
                'type' => 'campaign',
            ],
            [
                'name' => 'এতিম ও দুস্থ শিক্ষার্থীদের শিক্ষাবৃত্তি তহবিল',
                'description' => 'অর্থের অভাবে ঝরে পড়া মেধাবী শিক্ষার্থীদের খাতা, কলম, স্কুল ব্যাগ এবং মাসিক বৃত্তি প্রদান।',
                'type' => 'campaign',
            ],
        ];

        $funds = [];
        foreach ($fundsData as $fundItem) {
            $funds[] = Fund::create([
                'organization_id' => $orgId,
                'name' => $fundItem['name'],
                'description' => $fundItem['description'],
                'type' => $fundItem['type'],
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ]);
        }

        // 2. Seed Donors
        $donorsData = [
            ['name' => 'মোহাম্মদ আরিফুল রহমান', 'email' => 'ariful.dhaka@gmail.com', 'phone' => '01712345678', 'blood_group' => 'A+', 'address' => 'ধানমণ্ডি, ঢাকা'],
            ['name' => 'ফাতেমা আক্তার লিপি', 'email' => 'fatema.lipi@yahoo.com', 'phone' => '01815349876', 'blood_group' => 'O+', 'address' => 'হালিশহর, চট্টগ্রাম'],
            ['name' => 'আশরাফুল ইসলাম রনি', 'email' => 'ashrafrony@gmail.com', 'phone' => '01911456321', 'blood_group' => 'B+', 'address' => 'জিন্দাবাজার, সিলেট'],
            ['name' => 'নুসরাত জাহান রিয়া', 'email' => 'nusrat.riya@outlook.com', 'phone' => '01515987321', 'blood_group' => 'AB+', 'address' => 'উপশহর, রাজশাহী'],
            ['name' => 'তানভীর আহমেদ রিফাত', 'email' => 'tanvir.rifat@hotmail.com', 'phone' => '01309876543', 'blood_group' => 'O-', 'address' => 'খালিশপুর, খুলনা'],
            ['name' => 'সাদিয়া আক্তার নিপা', 'email' => 'sadiyanipa@gmail.com', 'phone' => '01799887766', 'blood_group' => 'A-', 'address' => 'সদর রোড, বরিশাল'],
            ['name' => 'আব্দুল্লাহ আল মামুন', 'email' => 'mamun.cse@gmail.com', 'phone' => '01899112233', 'blood_group' => 'B-', 'address' => 'ধাপ, রংপুর'],
            ['name' => 'ইঞ্জিনিয়ার কামরুল হাসান', 'email' => 'kamrul.engr@gmail.com', 'phone' => '01999887755', 'blood_group' => 'AB-', 'address' => 'উত্তরা, ঢাকা'],
            ['name' => 'হাজী জয়নাল আবেদীন', 'email' => 'haji.zoynal@yahoo.com', 'phone' => '01599884433', 'blood_group' => 'O+', 'address' => 'কোটবাড়ী, কুমিল্লা'],
            ['name' => 'মোসাম্মৎ সেলিনা বেগম', 'email' => 'selina.begum@gmail.com', 'phone' => '01399775511', 'blood_group' => 'A+', 'address' => 'চাষাড়া, নারায়ণগঞ্জ'],
            ['name' => 'তাসনিম সুলতানা মীম', 'email' => 'mim.tasnim@gmail.com', 'phone' => '01755112233', 'blood_group' => 'B+', 'address' => 'মিরপুর, ঢাকা'],
            ['name' => 'ড. আতিউর রহমান', 'email' => 'atiur.eco@gmail.com', 'phone' => '01855998877', 'blood_group' => 'AB+', 'address' => 'বনানী, ঢাকা'],
        ];

        $donors = [];
        foreach ($donorsData as $donorItem) {
            $donors[] = Donor::create([
                'organization_id' => $orgId,
                'name' => $donorItem['name'],
                'email' => $donorItem['email'],
                'phone' => $donorItem['phone'],
                'blood_group' => $donorItem['blood_group'],
                'address' => $donorItem['address'],
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ]);
        }

        // 3. Seed Transactions (Income & Expenses over last 6 months)
        // Let's create transactions for March, April, May, June, July, August 2026.
        // We will generate random transactions with a deterministic structure.
        
        $months = [
            '2026-03' => ['name' => 'March', 'days' => 31],
            '2026-04' => ['name' => 'April', 'days' => 30],
            '2026-05' => ['name' => 'May', 'days' => 31],
            '2026-06' => ['name' => 'June', 'days' => 30],
            '2026-07' => ['name' => 'July', 'days' => 31],
            '2026-08' => ['name' => 'August', 'days' => 31],
        ];

        $paymentMethods = ['bKash', 'Nagad', 'bank', 'cash'];

        // Main Fund is $funds[0]
        // Flood Relief is $funds[1]
        // Winter Clothing is $funds[2]
        // Medical Camp is $funds[3]
        // Education Fund is $funds[4]

        $txCounter = 100;

        foreach ($months as $monthYear => $monthInfo) {
            // Number of income transactions per month (between 4 and 7)
            $incomeCount = rand(4, 7);
            for ($i = 0; $i < $incomeCount; $i++) {
                $donor = $donors[array_rand($donors)];
                $fund = $funds[array_rand($funds)];
                
                // Set donation amount depending on fund type
                if ($fund->type === 'main') {
                    $amount = rand(5, 15) * 500; // 2500 to 7500
                } else {
                    $amount = rand(2, 20) * 1000; // 2000 to 20000
                }

                $day = rand(1, $monthInfo['days']);
                $dateStr = "{$monthYear}-" . sprintf('%02d', $day);
                $dateTime = Carbon::createFromFormat('Y-m-d', $dateStr)->setHour(rand(9, 21))->setMinute(rand(0, 59));
                
                $txCounter++;
                $txn_id = 'TXN' . $dateTime->timestamp . $txCounter;

                Transaction::create([
                    'organization_id' => $orgId,
                    'txn_id' => $txn_id,
                    'donor_id' => $donor->id,
                    'fund_id' => $fund->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'status' => 'completed',
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'purpose' => $fund->name . ' এ অনুদান',
                    'note' => $donor->name . ' কর্তৃক ' . $fund->name . ' এ অনুদান প্রদান।',
                    'created_by' => $adminUserId,
                    'updated_by' => $adminUserId,
                    'created_at' => $dateTime,
                    'updated_at' => $dateTime,
                ]);
            }

            // Number of expense transactions per month (between 2 and 4)
            $expenseCount = rand(2, 4);
            for ($e = 0; $e < $expenseCount; $e++) {
                $fund = $funds[array_rand($funds)];
                
                // Expense amount
                $amount = rand(3, 15) * 1000; // 3000 to 15000

                // Generate a random expense purpose
                $purposes = [
                    'ত্রাণ সামগ্রী ক্রয় ও প্যাকেজিং',
                    'মেডিকেল ক্যাম্পের ওষুধ ক্রয়',
                    'স্বেচ্ছাসেবকদের যাতায়াত ও খাবার খরচ',
                    'কম্বল ও শীতবস্ত্র পাইকারি ক্রয়',
                    'অসচ্ছল শিক্ষার্থীদের বৃত্তি বিতরণ',
                    'বই-খাতা ও কলম বিতরণ খরচ',
                    'মাইকিং ও প্রচার প্রচারণা খরচ',
                    'ব্যানার ও লিপলেট প্রিন্টিং',
                    'অফিস ভাড়া ও বিদ্যুৎ বিল',
                ];

                $day = rand(1, $monthInfo['days']);
                $dateStr = "{$monthYear}-" . sprintf('%02d', $day);
                $dateTime = Carbon::createFromFormat('Y-m-d', $dateStr)->setHour(rand(10, 18))->setMinute(rand(0, 59));

                $txCounter++;
                $txn_id = 'TXN' . $dateTime->timestamp . $txCounter;

                Transaction::create([
                    'organization_id' => $orgId,
                    'txn_id' => $txn_id,
                    'donor_id' => null,
                    'fund_id' => $fund->id,
                    'amount' => $amount,
                    'type' => 'debit',
                    'status' => 'completed',
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'purpose' => $purposes[array_rand($purposes)],
                    'note' => $fund->name . ' থেকে খরচ বাবদ।',
                    'created_by' => $adminUserId,
                    'updated_by' => $adminUserId,
                    'created_at' => $dateTime,
                    'updated_at' => $dateTime,
                ]);
            }
        }
    }
}
