<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donor;
use App\Models\Fund;
use App\Models\Transaction;
use App\Models\CampaignAdjustment;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create organization
        $organization = Organization::first() ?? Organization::create([
            'name' => 'মানবতা যুব সংঘ',
            'address' => 'ধানমণ্ডি, ঢাকা-১২০৫',
            'phone' => '01700000000',
            'email' => 'info@manobota.org',
            'website' => 'https://manobota.org',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        $orgId = $organization->id;
        $adminUser = User::where('organization_id', $orgId)->first() ?? User::first();
        $adminUserId = $adminUser ? $adminUser->id : 1;

        // Clean previous dummy records for this organization to prevent orphaned rows
        Transaction::where('organization_id', $orgId)->delete();
        CampaignAdjustment::where('organization_id', $orgId)->delete();
        Donor::where('organization_id', $orgId)->delete();
        Fund::where('organization_id', $orgId)->delete();

        // 1. Seed Comprehensive Funds (1 Main Fund + 7 Realistic Campaign Funds)
        $fundsData = [
            [
                'name' => 'মানবতা যুব সংঘ মেইন ফান্ড',
                'description' => 'সংগঠনের সাধারণ ও প্রশাসনিক ব্যয় এবং সার্বিক সমাজকল্যাণমূলক কার্যক্রম পরিচালনার মূল কেন্দ্রীয় তহবিল।',
                'type' => 'main',
            ],
            [
                'name' => 'সিলেট বন্যাদুর্গতদের জরুরি ত্রাণ তহবিল',
                'description' => 'সিলেটের বন্যাকবলিত প্রত্যন্ত অঞ্চলে শুকনো খাবার, বিশুদ্ধ খাবার পানি, স্যালাইন ও জরুরি ওষুধ বিতরণ।',
                'type' => 'campaign',
            ],
            [
                'name' => 'শীতার্তদের মাঝে কম্বল ও শীতবস্ত্র বিতরণ ২০২৬',
                'description' => 'উত্তরাঞ্চলের কুড়িগ্রাম, পঞ্চগড় ও ঠাকুরগাঁওয়ের হতদরিদ্র শীতার্ত মানুষের মাঝে উন্নত মানের কম্বল বিতরণ।',
                'type' => 'campaign',
            ],
            [
                'name' => 'ফ্রি ফ্রাইডে মেডিকেল ও হেলথ ক্যাম্প',
                'description' => 'সুবিধাবঞ্চিত মানুষের বিনামূল্যে ডায়াবেটিস পরীক্ষা, রক্তের গ্রুপ নির্ণয় ও বিশেষজ্ঞ চিকিৎসকের ব্যবস্থাপত্র প্রদান।',
                'type' => 'campaign',
            ],
            [
                'name' => 'এতিম ও অসচ্ছল শিক্ষার্থীদের শিক্ষাবৃত্তি তহবিল',
                'description' => 'মেধাবী অথচ অসচ্ছল শিক্ষার্থীদের প্রতি মাসে শিক্ষা উপবৃত্তি, নতুন বই, খাতা এবং স্কুল ব্যাগ প্রদান।',
                'type' => 'campaign',
            ],
            [
                'name' => 'পরিবেশ রক্ষা ও বৃক্ষরোপণ কর্মসূচি ২০২৬',
                'description' => 'জলবায়ু পরিবর্তনের প্রভাব মোকাবেলায় সারাদেশে ফলজ, বনজ ও ঔষধি গাছের চারা রোপণ ও বিতরণ।',
                'type' => 'campaign',
            ],
            [
                'name' => 'পবিত্র মাহে রমজান ফুড প্যাক ও ইফতার বিতরণ',
                'description' => 'পবিত্র রমজান উপলক্ষে অসহায় পরিবারের মাঝে এক মাসের নিত্যপ্রয়োজনীয় খাদ্যসামগ্রী ও ইফতার সামগ্রী বিতরণ।',
                'type' => 'campaign',
            ],
            [
                'name' => 'জরুরি অ্যাম্বুলেন্স ও অক্সিজেন সেবা ফান্ড',
                'description' => 'মুমূর্ষু রোগীদের জরুরি মুহূর্তে বিনামূল্যে অক্সিজেন সিলিন্ডার ও স্বল্পমূল্যে অ্যাম্বুলেন্স পরিবহন সেবা।',
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

        $mainFund = $funds[0];
        $campaignFunds = array_slice($funds, 1);

        // 2. Seed 18 Realistic Donors across Bangladesh
        $donorsData = [
            ['name' => 'মোহাম্মদ আরিফুল রহমান', 'email' => 'ariful.dhaka@gmail.com', 'phone' => '01711223344', 'blood_group' => 'A+', 'address' => 'ধানমণ্ডি, ঢাকা'],
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
            ['name' => 'ড. আতিউর রহমান', 'email' => 'atiur.eco@gmail.com', 'phone' => '01855998877', 'blood_group' => 'AB+', 'address' => 'গুলশান, ঢাকা'],
            ['name' => 'মাহমুদুল হাসান ফুয়াদ', 'email' => 'fuad.hasan@gmail.com', 'phone' => '01766554433', 'blood_group' => 'O+', 'address' => 'টউনহল, ময়মনসিংহ'],
            ['name' => 'ফারহানা ইয়াসমিন শম্পা', 'email' => 'farhana.shompa@gmail.com', 'phone' => '01922334455', 'blood_group' => 'A+', 'address' => 'চকবাজার, বগুড়া'],
            ['name' => 'কাজী মেহেরাব হোসেন', 'email' => 'mehrab.kazi@yahoo.com', 'phone' => '01844556677', 'blood_group' => 'B+', 'address' => 'চৌরাস্তা, গাজীপুর'],
            ['name' => 'ডা. সুমাইয়া ইসলাম', 'email' => 'dr.sumaiya@gmail.com', 'phone' => '01733445566', 'blood_group' => 'O+', 'address' => 'বনানী, ঢাকা'],
            ['name' => 'মীর মোয়াজ্জেম হোসেন', 'email' => 'mir.moazzem@gmail.com', 'phone' => '01677889900', 'blood_group' => 'AB+', 'address' => 'ফরিদপুর সদর'],
            ['name' => 'আফসানা চৌধুরী রোদেলা', 'email' => 'rodelach@gmail.com', 'phone' => '01533445566', 'blood_group' => 'A+', 'address' => 'আগ্রাবাদ, চট্টগ্রাম'],
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

        // 3. Seed Monthly Transactions across Last 6 Months (March - August 2026)
        $months = [
            '2026-03' => ['name' => 'March', 'days' => 31],
            '2026-04' => ['name' => 'April', 'days' => 30],
            '2026-05' => ['name' => 'May', 'days' => 31],
            '2026-06' => ['name' => 'June', 'days' => 30],
            '2026-07' => ['name' => 'July', 'days' => 31],
            '2026-08' => ['name' => 'August', 'days' => 31],
        ];

        $paymentMethods = ['bKash', 'Nagad', 'bank', 'cash'];
        $expensePurposes = [
            'ত্রাণ সামগ্রী (চাল, ডাল, তেল, স্যালাইন) ক্রয় ও পরিবহন',
            'মেডিকেল ক্যাম্পের ওষুধ ও সার্জিক্যাল সামগ্রী ক্রয়',
            'স্বেচ্ছাসেবকদের যাতায়াত ও খাদ্য সহায়তা বাবদ খরচ',
            'উন্নত মানের কম্বল ও শিশুদের শীতবস্ত্র পাইকারি ক্রয়',
            'অসচ্ছল শিক্ষার্থীদের শিক্ষাবৃত্তি ও বই-খাতা বিতরণ',
            'ফলজ ও ঔষধি গাছের চারা ক্রয় এবং রোপণ খরচ',
            'রমজান ফুড প্যাক প্যাকেটজাতকরণ ও বিতরণ লজিস্টিকস',
            'অক্সিজেন সিলিন্ডার রিফিল ও রেগুলেটর কিট ক্রয়',
            'মাইকিং, লিফলেট এবং সচেতনতামূলক ব্যানার প্রিন্টিং',
            'সংগঠনের অফিস ব্যবস্থাপনা ও ইন্টারনেট খরচ',
        ];

        $txCounter = 1000;

        foreach ($months as $monthYear => $monthInfo) {
            // A. Income Donations (6 to 10 donations per month)
            $incomeCount = rand(7, 10);
            for ($i = 0; $i < $incomeCount; $i++) {
                $donor = $donors[array_rand($donors)];
                $fund = $funds[array_rand($funds)];

                if ($fund->type === 'main') {
                    $amount = rand(3, 12) * 1000; // 3,000 to 12,000 BDT
                } else {
                    $amount = rand(2, 25) * 1000; // 2,000 to 25,000 BDT
                }

                $day = rand(1, $monthInfo['days']);
                $dateStr = "{$monthYear}-" . sprintf('%02d', $day);
                $dateTime = Carbon::createFromFormat('Y-m-d', $dateStr)->setHour(rand(8, 22))->setMinute(rand(0, 59));

                $txCounter++;
                $txn_id = 'TXN' . $dateTime->format('ymd') . $txCounter;

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

            // B. Expenses (3 to 6 expense transactions per month)
            $expenseCount = rand(3, 6);
            for ($e = 0; $e < $expenseCount; $e++) {
                $fund = $funds[array_rand($funds)];
                $amount = rand(2, 16) * 1000; // 2,000 to 16,000 BDT

                $day = rand(1, $monthInfo['days']);
                $dateStr = "{$monthYear}-" . sprintf('%02d', $day);
                $dateTime = Carbon::createFromFormat('Y-m-d', $dateStr)->setHour(rand(9, 19))->setMinute(rand(0, 59));

                $txCounter++;
                $txn_id = 'TXN' . $dateTime->format('ymd') . $txCounter;

                Transaction::create([
                    'organization_id' => $orgId,
                    'txn_id' => $txn_id,
                    'donor_id' => null,
                    'fund_id' => $fund->id,
                    'amount' => $amount,
                    'type' => 'debit',
                    'status' => 'completed',
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'purpose' => $expensePurposes[array_rand($expensePurposes)],
                    'note' => $fund->name . ' থেকে খরচ বাবদ।',
                    'created_by' => $adminUserId,
                    'updated_by' => $adminUserId,
                    'created_at' => $dateTime,
                    'updated_at' => $dateTime,
                ]);
            }
        }

        // 4. Seed Realistic Campaign Adjustments (Budget allocation and Surplus returns)
        // Adjustment 1: Allocate budget from Main Fund to Flood Relief
        $floodFund = $funds[1];
        $adjDate1 = Carbon::create(2026, 5, 10, 14, 30);
        $adj1 = CampaignAdjustment::create([
            'organization_id' => $orgId,
            'main_fund_id' => $mainFund->id,
            'campaign_fund_id' => $floodFund->id,
            'amount' => 15000,
            'type' => 'to_campaign',
            'note' => 'জরুরি বন্যার্তদের সহায়তার জন্য মেইন ফান্ড থেকে বিশেষ বরাদ্দ',
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate1,
            'updated_at' => $adjDate1,
        ]);

        Transaction::create([
            'organization_id' => $orgId,
            'txn_id' => 'TXN' . $adjDate1->format('ymd') . '2001',
            'fund_id' => $mainFund->id,
            'amount' => 15000,
            'type' => 'debit',
            'status' => 'completed',
            'payment_method' => 'bank',
            'purpose' => 'Transfer to Campaign: ' . $floodFund->name,
            'note' => 'ক্যাম্পেইনে তহবিল স্থানান্তর',
            'adjustment_id' => $adj1->id,
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate1,
            'updated_at' => $adjDate1,
        ]);

        Transaction::create([
            'organization_id' => $orgId,
            'txn_id' => 'TXN' . $adjDate1->format('ymd') . '2002',
            'fund_id' => $floodFund->id,
            'amount' => 15000,
            'type' => 'credit',
            'status' => 'completed',
            'payment_method' => 'bank',
            'purpose' => 'Received from Main Fund: ' . $mainFund->name,
            'note' => 'মেইন ফান্ড থেকে বরাদ্দ গ্রহণ',
            'adjustment_id' => $adj1->id,
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate1,
            'updated_at' => $adjDate1,
        ]);

        // Adjustment 2: Return surplus unspent budget from Winter Clothing Campaign to Main Fund
        $winterFund = $funds[2];
        $adjDate2 = Carbon::create(2026, 7, 20, 16, 45);
        $adj2 = CampaignAdjustment::create([
            'organization_id' => $orgId,
            'main_fund_id' => $mainFund->id,
            'campaign_fund_id' => $winterFund->id,
            'amount' => 8000,
            'type' => 'to_main',
            'note' => 'শীতবস্ত্র বিতরণ ক্যাম্পেইন সফলভাবে সমাপ্তির পর উদ্বৃত্ত অর্থ মেইন ফান্ডে ফেরত',
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate2,
            'updated_at' => $adjDate2,
        ]);

        Transaction::create([
            'organization_id' => $orgId,
            'txn_id' => 'TXN' . $adjDate2->format('ymd') . '2003',
            'fund_id' => $winterFund->id,
            'amount' => 8000,
            'type' => 'debit',
            'status' => 'completed',
            'payment_method' => 'bank',
            'purpose' => 'Transfer to Main Fund: ' . $mainFund->name,
            'note' => 'উদ্বৃত্ত তহবিল মূল অ্যাকাউন্টে স্থানান্তর',
            'adjustment_id' => $adj2->id,
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate2,
            'updated_at' => $adjDate2,
        ]);

        Transaction::create([
            'organization_id' => $orgId,
            'txn_id' => 'TXN' . $adjDate2->format('ymd') . '2004',
            'fund_id' => $mainFund->id,
            'amount' => 8000,
            'type' => 'credit',
            'status' => 'completed',
            'payment_method' => 'bank',
            'purpose' => 'Received from Campaign: ' . $winterFund->name,
            'note' => 'ক্যাম্পেইনের উদ্বৃত্ত তহবিল গ্রহণ',
            'adjustment_id' => $adj2->id,
            'created_by' => $adminUserId,
            'updated_by' => $adminUserId,
            'created_at' => $adjDate2,
            'updated_at' => $adjDate2,
        ]);
    }
}
