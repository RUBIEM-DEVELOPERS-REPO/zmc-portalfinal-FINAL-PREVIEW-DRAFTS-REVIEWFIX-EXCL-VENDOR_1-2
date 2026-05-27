<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Region;
use App\Models\Notice;
use App\Models\Event;
use App\Models\News;
use App\Models\SystemConfig;
use App\Models\Application;
use App\Models\CardTemplate;
use Spatie\Permission\Models\Role;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedStaffUsers();
        $this->seedRegions();
        $this->seedNotices();
        $this->seedEvents();
        $this->seedNews();
        $this->seedSystemConfigs();
        $this->seedTestApplications();
        $this->seedCardTemplates();
    }

    private function seedStaffUsers(): void
    {
        $staff = [
            [
                'name' => 'Accreditation Officer',
                'email' => 'officer@zmc.org.zw',
                'role' => 'accreditation_officer',
                'designation' => 'Accreditation Officer',
            ],
            [
                'name' => 'Registrar General',
                'email' => 'registrar@zmc.org.zw',
                'role' => 'registrar',
                'designation' => 'Registrar',
            ],
            [
                'name' => 'Accounts Officer',
                'email' => 'accounts@zmc.org.zw',
                'role' => 'accounts_payments',
                'designation' => 'Accounts Officer',
            ],
            [
                'name' => 'Production Manager',
                'email' => 'production@zmc.org.zw',
                'role' => 'production',
                'designation' => 'Production Manager',
            ],
            [
                'name' => 'IT Administrator',
                'email' => 'itadmin@zmc.org.zw',
                'role' => 'it_admin',
                'designation' => 'IT Administrator',
            ],
            [
                'name' => 'Audit Officer',
                'email' => 'auditor@zmc.org.zw',
                'role' => 'auditor',
                'designation' => 'Audit Officer',
            ],
            [
                'name' => 'Director General',
                'email' => 'director@zmc.org.zw',
                'role' => 'director',
                'designation' => 'Director General',
            ],
            [
                'name' => 'Complaints Officer',
                'email' => 'complaints@zmc.org.zw',
                'role' => 'complaints_officer',
                'designation' => 'Complaints Officer',
            ],
            [
                'name' => 'PR Officer',
                'email' => 'pr@zmc.org.zw',
                'role' => 'pr_officer',
                'designation' => 'Public Relations Officer',
            ],
            [
                'name' => 'Compliance Officer',
                'email' => 'compliance@zmc.org.zw',
                'role' => 'public_info_compliance',
                'designation' => 'Public Information & Compliance Officer',
            ],
            [
                'name' => 'Research Officer',
                'email' => 'research@zmc.org.zw',
                'role' => 'research_training',
                'designation' => 'Research, Training & Standards Officer',
            ],
            [
                'name' => 'Chief Accountant',
                'email' => 'chiefaccountant@zmc.org.zw',
                'role' => 'chief_accountant',
                'designation' => 'Chief Accountant',
            ],
        ];

        foreach ($staff as $s) {
            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('Staff@12345'),
                    'account_status' => 'active',
                    'account_type' => 'staff',
                    'designation' => $s['designation'],
                ]
            );

            if (!$user->hasRole($s['role'])) {
                $user->assignRole($s['role']);
            }
        }
    }

    private function seedRegions(): void
    {
        $regions = [
            ['name' => 'Harare', 'code' => 'HRE'],
            ['name' => 'Bulawayo', 'code' => 'BYO'],
            ['name' => 'Mutare', 'code' => 'MUT'],
            ['name' => 'Masvingo', 'code' => 'MSV'],
            ['name' => 'Gweru', 'code' => 'GWR'],
            ['name' => 'Chinhoyi', 'code' => 'CHN'],
            ['name' => 'Bindura', 'code' => 'BND'],
            ['name' => 'Marondera', 'code' => 'MRD'],
            ['name' => 'Hwange', 'code' => 'HWG'],
            ['name' => 'Kadoma', 'code' => 'KDM'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['code' => $region['code']],
                [
                    'name' => $region['name'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedNotices(): void
    {
        $adminId = User::where('email', 'superadmin@zmc.org.zw')->value('id') ?? 1;

        $notices = [
            [
                'title' => '2026 Accreditation Season Now Open',
                'body' => 'The Zimbabwe Media Commission is pleased to announce that the 2026 accreditation season is now open. All media practitioners are encouraged to apply for new press cards or renew their existing accreditation. Applications can be submitted through this portal. The deadline for submissions is 30 June 2026.',
                'target_portal' => 'journalist',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Media House Registration Renewal Reminder',
                'body' => 'All registered media houses are reminded that annual registration renewals are due by 31 March 2026. Failure to renew may result in suspension of operating licenses. Please submit your renewal applications through the Media House portal with all required documents.',
                'target_portal' => 'mediahouse',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Updated Documentation Requirements',
                'body' => 'Please note the following updated documentation requirements for accreditation applications: 1) Valid national ID or passport, 2) Recent passport-sized photograph, 3) Letter of employment from media house, 4) Portfolio of published work (for freelancers). All documents must be uploaded in PDF or JPEG format.',
                'target_portal' => 'journalist',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Regional Collection Offices Operating Hours',
                'body' => 'All regional collection offices operate Monday to Friday from 8:00 AM to 4:30 PM. Harare Head Office: 7th Floor, Fidelity Life Tower, Raleigh Street. Bulawayo Office: Suite 203, Masiye Phambili Building, Fort Street. Mutare Office: Room 12, Meikles Building, Herbert Chitepo Street.',
                'target_portal' => 'both',
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
        ];

        foreach ($notices as $notice) {
            Notice::updateOrCreate(
                ['title' => $notice['title']],
                array_merge($notice, ['created_by' => $adminId])
            );
        }
    }

    private function seedEvents(): void
    {
        $adminId = User::where('email', 'superadmin@zmc.org.zw')->value('id') ?? 1;

        $events = [
            [
                'title' => 'World Press Freedom Day Celebration',
                'description' => 'Join the Zimbabwe Media Commission in celebrating World Press Freedom Day. The event will feature keynote speeches, panel discussions on media freedom in Zimbabwe, and an awards ceremony for outstanding journalism. All accredited media practitioners are invited.',
                'starts_at' => now()->addMonths(2)->setTime(9, 0),
                'ends_at' => now()->addMonths(2)->setTime(17, 0),
                'location' => 'Rainbow Towers Hotel, Harare',
                'target_portal' => 'journalist',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Media Ethics Workshop',
                'description' => 'A mandatory professional development workshop covering media ethics, responsible reporting, and the ZMC Code of Conduct. Topics include: digital journalism ethics, source protection, fact-checking, and reporting on sensitive issues. CPD points will be awarded to participants.',
                'starts_at' => now()->addMonth()->setTime(8, 30),
                'ends_at' => now()->addMonth()->setTime(16, 30),
                'location' => 'Meikles Hotel Conference Centre, Harare',
                'target_portal' => 'journalist',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Media House Compliance Briefing',
                'description' => 'An important briefing session for all registered media houses on compliance requirements for 2026. The session will cover regulatory updates, reporting obligations, and new guidelines for digital media operations.',
                'starts_at' => now()->addWeeks(3)->setTime(10, 0),
                'ends_at' => now()->addWeeks(3)->setTime(13, 0),
                'location' => 'ZMC Head Office, Harare',
                'target_portal' => 'mediahouse',
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['title' => $event['title']],
                array_merge($event, ['created_by' => $adminId])
            );
        }
    }

    private function seedNews(): void
    {
        $adminId = User::where('email', 'superadmin@zmc.org.zw')->value('id') ?? 1;

        $news = [
            [
                'title' => 'ZMC Launches New Online Portal',
                'slug' => 'zmc-launches-new-online-portal',
                'body' => 'The Zimbabwe Media Commission has officially launched its new online portal for media practitioner accreditation and media house registration. The portal enables media practitioners to apply for accreditation, track application status, and manage their profiles entirely online. Media houses can also register and renew their operating licenses through the system. This digital transformation initiative is part of ZMC\'s commitment to improving service delivery and reducing turnaround times.',
                'is_published' => true,
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Record Number of Accreditation Applications Received',
                'slug' => 'record-accreditation-applications-2026',
                'body' => 'The Zimbabwe Media Commission has received a record number of accreditation applications for the 2026 season. Over 2,000 applications have been submitted through the new online portal since its launch. ZMC Chairperson expressed satisfaction with the adoption of the digital platform, noting that the streamlined process has significantly reduced processing times from weeks to days.',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'ZMC Partners with International Press Institute',
                'slug' => 'zmc-partners-international-press-institute',
                'body' => 'The Zimbabwe Media Commission has entered into a partnership agreement with the International Press Institute (IPI) to promote press freedom and professional journalism standards in Zimbabwe. The partnership will include joint training programmes, exchange visits, and collaborative research on media development. The initiative underscores ZMC\'s commitment to aligning with international best practices in media regulation.',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
        ];

        foreach ($news as $article) {
            News::updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, ['created_by' => $adminId])
            );
        }
    }

    private function seedTestApplications(): void
    {
        $testApplicants = [
            [
                'name' => 'John Moyo',
                'email' => 'john.moyo@example.com',
                'portal_type' => 'journalist',
            ],
            [
                'name' => 'Mary Ndlovu',
                'email' => 'mary.ndlovu@example.com',
                'portal_type' => 'journalist',
            ],
            [
                'name' => 'Tatenda Chirwa',
                'email' => 'tatenda.chirwa@example.com',
                'portal_type' => 'journalist',
            ],
            [
                'name' => 'Daily Herald Media',
                'email' => 'admin@dailyherald.co.zw',
                'portal_type' => 'mediahouse',
            ],
            [
                'name' => 'Renewal Applicant',
                'email' => 'renew@example.com',
                'portal_type' => 'journalist',
            ],
        ];

        foreach ($testApplicants as $ta) {
            User::updateOrCreate(
                ['email' => $ta['email']],
                [
                    'name' => $ta['name'],
                    'password' => Hash::make('Test@12345'),
                    'account_status' => 'active',
                    'account_type' => 'public',
                ]
            );
        }

        $john = User::where('email', 'john.moyo@example.com')->first();
        $mary = User::where('email', 'mary.ndlovu@example.com')->first();
        $tatenda = User::where('email', 'tatenda.chirwa@example.com')->first();
        $herald = User::where('email', 'admin@dailyherald.co.zw')->first();
        $renewUser = User::where('email', 'renew@example.com')->first();

        if ($john && Application::where('reference', 'ZMC-ACC-2026-0001')->doesntExist()) {
            Application::create([
                'reference' => 'ZMC-ACC-2026-0001',
                'applicant_user_id' => $john->id,
                'application_type' => 'accreditation',
                'request_type' => 'new',
                'status' => Application::SUBMITTED,
                'form_data' => [
                    'first_name' => 'John', 'last_name' => 'Moyo',
                    'id_number' => '63-123456-A-78',
                    'category' => 'print', 'scope' => 'local',
                    'employer' => 'The Herald', 'designation' => 'Reporter',
                ],
                'is_draft' => false,
                'submitted_at' => now()->subDays(3),
                'collection_region' => 'Harare',
            ]);
        }

        if ($mary && Application::where('reference', 'ZMC-ACC-2026-0002')->doesntExist()) {
            Application::create([
                'reference' => 'ZMC-ACC-2026-0002',
                'applicant_user_id' => $mary->id,
                'application_type' => 'accreditation',
                'request_type' => 'new',
                'status' => Application::APPROVED_AWAITING_PAYMENT,
                'form_data' => [
                    'first_name' => 'Mary', 'last_name' => 'Ndlovu',
                    'id_number' => '29-654321-B-42',
                    'category' => 'broadcast', 'scope' => 'local',
                    'employer' => 'ZBC', 'designation' => 'News Anchor',
                ],
                'is_draft' => false,
                'submitted_at' => now()->subDays(5),
                'collection_region' => 'Bulawayo',
            ]);
        }

        if ($tatenda && Application::where('reference', 'ZMC-ACC-2026-0003')->doesntExist()) {
            Application::create([
                'reference' => 'ZMC-ACC-2026-0003',
                'applicant_user_id' => $tatenda->id,
                'application_type' => 'accreditation',
                'request_type' => 'new',
                'status' => Application::PAYMENT_VERIFIED,
                'form_data' => [
                    'first_name' => 'Tatenda', 'last_name' => 'Chirwa',
                    'id_number' => '42-789012-C-15',
                    'category' => 'online', 'scope' => 'local',
                    'employer' => 'Freelance', 'designation' => 'Media Practitioner',
                ],
                'is_draft' => false,
                'submitted_at' => now()->subDays(10),
                'collection_region' => 'Mutare',
            ]);
        }

        if ($herald && Application::where('reference', 'ZMC-REG-2026-0001')->doesntExist()) {
            Application::create([
                'reference' => 'ZMC-REG-2026-0001',
                'applicant_user_id' => $herald->id,
                'application_type' => 'media_house',
                'request_type' => 'new',
                'status' => Application::SUBMITTED_WITH_APP_FEE,
                'payment_stage' => 'application_fee',
                'form_data' => [
                    'media_house_name' => 'Daily Herald Media',
                    'registration_number' => 'REG/2026/001',
                    'media_type' => 'print',
                    'address' => '123 Samora Machel Ave, Harare',
                    'contact_phone' => '+263 242 700 000',
                ],
                'is_draft' => false,
                'submitted_at' => now()->subDays(2),
                'collection_region' => 'Harare',
            ]);
        }

        if ($renewUser && Application::where('reference', 'ZMC-ACC-2026-0004')->doesntExist()) {
            Application::create([
                'reference' => 'ZMC-ACC-2026-0004',
                'applicant_user_id' => $renewUser->id,
                'application_type' => 'accreditation',
                'request_type' => 'renewal',
                'status' => Application::AWAITING_ACCOUNTS_VERIFICATION,
                'form_data' => [
                    'first_name' => 'Renewal', 'last_name' => 'Applicant',
                    'accreditation_number' => 'ZMC/ACC/2025/1234',
                    'category' => 'print', 'scope' => 'local',
                ],
                'is_draft' => false,
                'submitted_at' => now()->subDay(),
                'collection_region' => 'Harare',
            ]);
        }
    }

    private function seedCardTemplates(): void
    {
        $productionUser = User::where('email', 'production@zmc.org.zw')->first();
        $createdBy = $productionUser ? $productionUser->id : 1;

        CardTemplate::updateOrCreate(
            ['name' => 'Standard Press Card 2026'],
            [
                'type' => 'card',
                'year' => 2026,
                'layout_config' => [
                    'fields' => [
                        ['name' => 'full_name', 'label' => 'Name', 'x' => 200, 'y' => 180, 'width' => 400, 'height' => 40, 'fontSize' => 22, 'color' => '#000000'],
                        ['name' => 'accreditation_number', 'label' => 'Accred No.', 'x' => 200, 'y' => 230, 'width' => 300, 'height' => 30, 'fontSize' => 16, 'color' => '#333333'],
                        ['name' => 'category', 'label' => 'Category', 'x' => 200, 'y' => 270, 'width' => 200, 'height' => 30, 'fontSize' => 14, 'color' => '#555555'],
                        ['name' => 'expiry_date', 'label' => 'Expiry', 'x' => 200, 'y' => 310, 'width' => 200, 'height' => 30, 'fontSize' => 14, 'color' => '#555555'],
                        ['name' => 'photo', 'label' => 'Photo', 'x' => 30, 'y' => 150, 'width' => 150, 'height' => 180],
                        ['name' => 'qr_code', 'label' => 'QR Code', 'x' => 680, 'y' => 350, 'width' => 120, 'height' => 120],
                    ],
                ],
                'is_active' => true,
                'created_by' => $createdBy,
            ]
        );

        CardTemplate::updateOrCreate(
            ['name' => 'Standard Certificate 2026'],
            [
                'type' => 'certificate',
                'year' => 2026,
                'layout_config' => [
                    'fields' => [
                        ['name' => 'media_house_name', 'label' => 'Media House', 'x' => 150, 'y' => 200, 'width' => 550, 'height' => 50, 'fontSize' => 28, 'color' => '#1a3a1a'],
                        ['name' => 'registration_number', 'label' => 'Reg No.', 'x' => 150, 'y' => 260, 'width' => 400, 'height' => 30, 'fontSize' => 16, 'color' => '#333333'],
                        ['name' => 'category', 'label' => 'Category', 'x' => 150, 'y' => 300, 'width' => 300, 'height' => 30, 'fontSize' => 14, 'color' => '#555555'],
                        ['name' => 'expiry_date', 'label' => 'Valid Until', 'x' => 150, 'y' => 340, 'width' => 300, 'height' => 30, 'fontSize' => 14, 'color' => '#555555'],
                        ['name' => 'qr_code', 'label' => 'QR Code', 'x' => 680, 'y' => 400, 'width' => 100, 'height' => 100],
                    ],
                ],
                'is_active' => true,
                'created_by' => $createdBy,
            ]
        );
    }

    private function seedSystemConfigs(): void
    {
        $configs = [
            ['key' => 'accreditation_fee_local_new', 'value' => '50'],
            ['key' => 'accreditation_fee_local_renewal', 'value' => '30'],
            ['key' => 'accreditation_fee_foreign_new', 'value' => '150'],
            ['key' => 'accreditation_fee_foreign_renewal', 'value' => '100'],
            ['key' => 'registration_fee_media_house_new', 'value' => '500'],
            ['key' => 'registration_fee_media_house_renewal', 'value' => '300'],
            ['key' => 'card_validity_years', 'value' => '1'],
            ['key' => 'application_processing_days', 'value' => '14'],
        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['key' => $config['key']],
                ['value' => $config['value']]
            );
        }
    }
}
