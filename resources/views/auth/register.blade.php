<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | ZMC Online Portal</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #111827;
            --primary: #1a1a1a;
            --primary-hover: #111111;
            --accent: #facc15;
            --accent-dark: #eab308;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }

        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #000 url('/zmc_building.png') no-repeat center center;
            background-size: cover;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }
        body::before{
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.55);
            pointer-events: none;
        }

        .wrap{ width: 100%; max-width: 480px; position: relative; z-index: 1; }

        /* BRAND SECTION */
        .brand{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            text-decoration: none;
        }
        .brand img {
            height: 70px;
            width: 70px;
            background: white;
            padding: 6px;
            border-radius: 50%;
            object-fit: contain;
            box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        }
        .brand span{
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 900;
            font-size: 20px;
            color: var(--text);
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .card{
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 30px;
        }

        .header{ text-align: center; margin-bottom: 20px; }
        .title{ margin: 0 0 8px; font-size: 22px; font-weight: 800; color: #111827; }
        .subtitle{ margin: 0; font-size: 13px; color: var(--muted); }

        /* FORM STYLING */
        .grid{ 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 12px; 
            margin-bottom: 15px;
        }

        .field{ margin-bottom: 15px; }
        label{ 
            display: block; 
            font-size: 12px; 
            font-weight: 700; 
            margin-bottom: 4px; 
            color: #111827;
        }

        .input-group{ position: relative; }
        .input{
            width: 100%;
            height: 40px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }
        .input:focus{ 
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.15);
        }

        .toggle{
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .btn{
            width: 100%;
            height: 44px;
            background-color: var(--primary);
            color: var(--accent);
            border: 2px solid var(--accent);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
            transition: all 0.2s ease;
        }
        .btn:hover{ 
            background-color: var(--accent);
            color: var(--primary);
            border-color: var(--primary);
        }

        .divider{
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .divider::before, .divider::after{
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .divider span{ padding: 0 10px; }

        .footer-link{
            text-align: center;
            font-size: 13px;
            font-weight: 700;
        }
        .footer-link a{
            color: var(--primary);
            text-decoration: none;
        }
        .footer-link a:hover{
            color: var(--accent-dark);
            text-decoration: underline;
        }
        .footer-link a:hover{ text-decoration: underline; }

        .error{ color: var(--danger); font-size: 12px; margin-top: 5px; font-weight: 600; }

        @media (max-width: 480px){
            .grid{ grid-template-columns: 1fr; }
            .card{ padding: 25px; }
        }
    </style>
</head>

<body class="landing-page">
<div class="page">
    <div class="wrap">
        @php
            $selectedPortal = session('public_selected_portal', 'journalist');
            $isMediaHouse = $selectedPortal === 'mass_media';
        @endphp
        {{-- BRAND SECTION WITH IMAGE LOGO --}}
        <div class="brand">
            <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
            <span>ZMC PORTAL</span>
        </div>

        <div class="card">
            <div class="header">
                <h1 class="title">Create Account</h1>
                <p class="subtitle">One account for all ZMC digital services.</p>
            </div>

            <form method="POST" action="{{ route('auth.register.store') }}">

                @csrf

                @if($isMediaHouse)
                    <div class="field">
                        <label for="organization_name">Organization's Name</label>
                        <input id="organization_name" name="organization_name" type="text" class="input" value="{{ old('organization_name') }}" required autofocus>
                        @error('organization_name') <div class="error">{{ $message }}</div> @enderror
                    </div>
                @else
                    <div class="grid">
                        <div>
                            <label for="first_name">Name</label>
                            <input id="first_name" name="first_name" type="text" class="input" value="{{ old('first_name') }}" required autofocus>
                            @error('first_name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="last_name">Surname</label>
                            <input id="last_name" name="last_name" type="text" class="input" value="{{ old('last_name') }}" required>
                            @error('last_name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endif

                <div class="field">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="grid">
                    <div>
                        <label for="phone_country_code">Country Code</label>
                        @php
                            // helper: compute flag emoji from ISO 3166-1 alpha-2 code
                            function flagFromIso($iso) {
                                if (!$iso) return '';
                                $iso = strtoupper($iso);
                                $flag = '';
                                for ($i = 0; $i < strlen($iso); $i++) {
                                    $codepoint = 127397 + ord($iso[$i]);
                                    $flag .= mb_convert_encoding('&#'.$codepoint.';', 'UTF-8', 'HTML-ENTITIES');
                                }
                                return $flag;
                            }

                            $countryCodes = [
                                ['code' => '+93', 'name' => 'Afghanistan', 'iso' => 'AF'],
                                ['code' => '+355', 'name' => 'Albania', 'iso' => 'AL'],
                                ['code' => '+213', 'name' => 'Algeria', 'iso' => 'DZ'],
                                ['code' => '+1684', 'name' => 'American Samoa', 'iso' => 'AS'],
                                ['code' => '+376', 'name' => 'Andorra', 'iso' => 'AD'],
                                ['code' => '+244', 'name' => 'Angola', 'iso' => 'AO'],
                                ['code' => '+1264', 'name' => 'Anguilla', 'iso' => 'AI'],
                                ['code' => '+1268', 'name' => 'Antigua and Barbuda', 'iso' => 'AG'],
                                ['code' => '+54', 'name' => 'Argentina', 'iso' => 'AR'],
                                ['code' => '+374', 'name' => 'Armenia', 'iso' => 'AM'],
                                ['code' => '+297', 'name' => 'Aruba', 'iso' => 'AW'],
                                ['code' => '+61', 'name' => 'Australia', 'iso' => 'AU'],
                                ['code' => '+43', 'name' => 'Austria', 'iso' => 'AT'],
                                ['code' => '+994', 'name' => 'Azerbaijan', 'iso' => 'AZ'],
                                ['code' => '+1242', 'name' => 'Bahamas', 'iso' => 'BS'],
                                ['code' => '+973', 'name' => 'Bahrain', 'iso' => 'BH'],
                                ['code' => '+880', 'name' => 'Bangladesh', 'iso' => 'BD'],
                                ['code' => '+1246', 'name' => 'Barbados', 'iso' => 'BB'],
                                ['code' => '+375', 'name' => 'Belarus', 'iso' => 'BY'],
                                ['code' => '+32', 'name' => 'Belgium', 'iso' => 'BE'],
                                ['code' => '+501', 'name' => 'Belize', 'iso' => 'BZ'],
                                ['code' => '+229', 'name' => 'Benin', 'iso' => 'BJ'],
                                ['code' => '+1441', 'name' => 'Bermuda', 'iso' => 'BM'],
                                ['code' => '+975', 'name' => 'Bhutan', 'iso' => 'BT'],
                                ['code' => '+591', 'name' => 'Bolivia', 'iso' => 'BO'],
                                ['code' => '+387', 'name' => 'Bosnia and Herzegovina', 'iso' => 'BA'],
                                ['code' => '+267', 'name' => 'Botswana', 'iso' => 'BW'],
                                ['code' => '+55', 'name' => 'Brazil', 'iso' => 'BR'],
                                ['code' => '+246', 'name' => 'British Indian Ocean Territory', 'iso' => 'IO'],
                                ['code' => '+1284', 'name' => 'British Virgin Islands', 'iso' => 'VG'],
                                ['code' => '+673', 'name' => 'Brunei', 'iso' => 'BN'],
                                ['code' => '+359', 'name' => 'Bulgaria', 'iso' => 'BG'],
                                ['code' => '+226', 'name' => 'Burkina Faso', 'iso' => 'BF'],
                                ['code' => '+257', 'name' => 'Burundi', 'iso' => 'BI'],
                                ['code' => '+855', 'name' => 'Cambodia', 'iso' => 'KH'],
                                ['code' => '+237', 'name' => 'Cameroon', 'iso' => 'CM'],
                                ['code' => '+1', 'name' => 'Canada', 'iso' => 'CA'],
                                ['code' => '+238', 'name' => 'Cape Verde', 'iso' => 'CV'],
                                ['code' => '+1345', 'name' => 'Cayman Islands', 'iso' => 'KY'],
                                ['code' => '+236', 'name' => 'Central African Republic', 'iso' => 'CF'],
                                ['code' => '+235', 'name' => 'Chad', 'iso' => 'TD'],
                                ['code' => '+56', 'name' => 'Chile', 'iso' => 'CL'],
                                ['code' => '+86', 'name' => 'China', 'iso' => 'CN'],
                                ['code' => '+57', 'name' => 'Colombia', 'iso' => 'CO'],
                                ['code' => '+269', 'name' => 'Comoros', 'iso' => 'KM'],
                                ['code' => '+242', 'name' => 'Congo (Brazzaville)', 'iso' => 'CG'],
                                ['code' => '+243', 'name' => 'Congo (DRC)', 'iso' => 'CD'],
                                ['code' => '+682', 'name' => 'Cook Islands', 'iso' => 'CK'],
                                ['code' => '+506', 'name' => 'Costa Rica', 'iso' => 'CR'],
                                ['code' => '+225', 'name' => 'Côte d\'Ivoire', 'iso' => 'CI'],
                                ['code' => '+385', 'name' => 'Croatia', 'iso' => 'HR'],
                                ['code' => '+53', 'name' => 'Cuba', 'iso' => 'CU'],
                                ['code' => '+599', 'name' => 'Curaçao', 'iso' => 'CW'],
                                ['code' => '+357', 'name' => 'Cyprus', 'iso' => 'CY'],
                                ['code' => '+420', 'name' => 'Czech Republic', 'iso' => 'CZ'],
                                ['code' => '+45', 'name' => 'Denmark', 'iso' => 'DK'],
                                ['code' => '+253', 'name' => 'Djibouti', 'iso' => 'DJ'],
                                ['code' => '+1767', 'name' => 'Dominica', 'iso' => 'DM'],
                                ['code' => '+1', 'name' => 'Dominican Republic', 'iso' => 'DO'],
                                ['code' => '+593', 'name' => 'Ecuador', 'iso' => 'EC'],
                                ['code' => '+20', 'name' => 'Egypt', 'iso' => 'EG'],
                                ['code' => '+503', 'name' => 'El Salvador', 'iso' => 'SV'],
                                ['code' => '+240', 'name' => 'Equatorial Guinea', 'iso' => 'GQ'],
                                ['code' => '+291', 'name' => 'Eritrea', 'iso' => 'ER'],
                                ['code' => '+372', 'name' => 'Estonia', 'iso' => 'EE'],
                                ['code' => '+251', 'name' => 'Ethiopia', 'iso' => 'ET'],
                                ['code' => '+500', 'name' => 'Falkland Islands', 'iso' => 'FK'],
                                ['code' => '+298', 'name' => 'Faroe Islands', 'iso' => 'FO'],
                                ['code' => '+679', 'name' => 'Fiji', 'iso' => 'FJ'],
                                ['code' => '+358', 'name' => 'Finland', 'iso' => 'FI'],
                                ['code' => '+33', 'name' => 'France', 'iso' => 'FR'],
                                ['code' => '+594', 'name' => 'French Guiana', 'iso' => 'GF'],
                                ['code' => '+689', 'name' => 'French Polynesia', 'iso' => 'PF'],
                                ['code' => '+241', 'name' => 'Gabon', 'iso' => 'GA'],
                                ['code' => '+220', 'name' => 'Gambia', 'iso' => 'GM'],
                                ['code' => '+995', 'name' => 'Georgia', 'iso' => 'GE'],
                                ['code' => '+49', 'name' => 'Germany', 'iso' => 'DE'],
                                ['code' => '+233', 'name' => 'Ghana', 'iso' => 'GH'],
                                ['code' => '+350', 'name' => 'Gibraltar', 'iso' => 'GI'],
                                ['code' => '+30', 'name' => 'Greece', 'iso' => 'GR'],
                                ['code' => '+299', 'name' => 'Greenland', 'iso' => 'GL'],
                                ['code' => '+1473', 'name' => 'Grenada', 'iso' => 'GD'],
                                ['code' => '+590', 'name' => 'Guadeloupe / Saint Martin', 'iso' => 'GP'],
                                ['code' => '+1671', 'name' => 'Guam', 'iso' => 'GU'],
                                ['code' => '+502', 'name' => 'Guatemala', 'iso' => 'GT'],
                                ['code' => '+44', 'name' => 'Guernsey / Isle of Man / Jersey', 'iso' => 'GB'],
                                ['code' => '+224', 'name' => 'Guinea', 'iso' => 'GN'],
                                ['code' => '+245', 'name' => 'Guinea-Bissau', 'iso' => 'GW'],
                                ['code' => '+592', 'name' => 'Guyana', 'iso' => 'GY'],
                                ['code' => '+509', 'name' => 'Haiti', 'iso' => 'HT'],
                                ['code' => '+504', 'name' => 'Honduras', 'iso' => 'HN'],
                                ['code' => '+852', 'name' => 'Hong Kong', 'iso' => 'HK'],
                                ['code' => '+36', 'name' => 'Hungary', 'iso' => 'HU'],
                                ['code' => '+354', 'name' => 'Iceland', 'iso' => 'IS'],
                                ['code' => '+91', 'name' => 'India', 'iso' => 'IN'],
                                ['code' => '+62', 'name' => 'Indonesia', 'iso' => 'ID'],
                                ['code' => '+98', 'name' => 'Iran', 'iso' => 'IR'],
                                ['code' => '+964', 'name' => 'Iraq', 'iso' => 'IQ'],
                                ['code' => '+353', 'name' => 'Ireland', 'iso' => 'IE'],
                                ['code' => '+972', 'name' => 'Israel', 'iso' => 'IL'],
                                ['code' => '+39', 'name' => 'Italy', 'iso' => 'IT'],
                                ['code' => '+1876', 'name' => 'Jamaica', 'iso' => 'JM'],
                                ['code' => '+81', 'name' => 'Japan', 'iso' => 'JP'],
                                ['code' => '+44', 'name' => 'Jersey / Guernsey / Isle of Man', 'iso' => 'GB'],
                                ['code' => '+962', 'name' => 'Jordan', 'iso' => 'JO'],
                                ['code' => '+7', 'name' => 'Kazakhstan / Russia', 'iso' => 'RU'],
                                ['code' => '+254', 'name' => 'Kenya', 'iso' => 'KE'],
                                ['code' => '+686', 'name' => 'Kiribati', 'iso' => 'KI'],
                                ['code' => '+383', 'name' => 'Kosovo', 'iso' => 'XK'],
                                ['code' => '+965', 'name' => 'Kuwait', 'iso' => 'KW'],
                                ['code' => '+996', 'name' => 'Kyrgyzstan', 'iso' => 'KG'],
                                ['code' => '+856', 'name' => 'Laos', 'iso' => 'LA'],
                                ['code' => '+371', 'name' => 'Latvia', 'iso' => 'LV'],
                                ['code' => '+961', 'name' => 'Lebanon', 'iso' => 'LB'],
                                ['code' => '+266', 'name' => 'Lesotho', 'iso' => 'LS'],
                                ['code' => '+231', 'name' => 'Liberia', 'iso' => 'LR'],
                                ['code' => '+218', 'name' => 'Libya', 'iso' => 'LY'],
                                ['code' => '+423', 'name' => 'Liechtenstein', 'iso' => 'LI'],
                                ['code' => '+370', 'name' => 'Lithuania', 'iso' => 'LT'],
                                ['code' => '+352', 'name' => 'Luxembourg', 'iso' => 'LU'],
                                ['code' => '+853', 'name' => 'Macau', 'iso' => 'MO'],
                                ['code' => '+389', 'name' => 'North Macedonia', 'iso' => 'MK'],
                                ['code' => '+261', 'name' => 'Madagascar', 'iso' => 'MG'],
                                ['code' => '+265', 'name' => 'Malawi', 'iso' => 'MW'],
                                ['code' => '+60', 'name' => 'Malaysia', 'iso' => 'MY'],
                                ['code' => '+960', 'name' => 'Maldives', 'iso' => 'MV'],
                                ['code' => '+223', 'name' => 'Mali', 'iso' => 'ML'],
                                ['code' => '+356', 'name' => 'Malta', 'iso' => 'MT'],
                                ['code' => '+692', 'name' => 'Marshall Islands', 'iso' => 'MH'],
                                ['code' => '+596', 'name' => 'Martinique', 'iso' => 'MQ'],
                                ['code' => '+222', 'name' => 'Mauritania', 'iso' => 'MR'],
                                ['code' => '+230', 'name' => 'Mauritius', 'iso' => 'MU'],
                                ['code' => '+262', 'name' => 'Mayotte / Réunion', 'iso' => 'RE'],
                                ['code' => '+52', 'name' => 'Mexico', 'iso' => 'MX'],
                                ['code' => '+691', 'name' => 'Micronesia', 'iso' => 'FM'],
                                ['code' => '+373', 'name' => 'Moldova', 'iso' => 'MD'],
                                ['code' => '+377', 'name' => 'Monaco', 'iso' => 'MC'],
                                ['code' => '+976', 'name' => 'Mongolia', 'iso' => 'MN'],
                                ['code' => '+382', 'name' => 'Montenegro', 'iso' => 'ME'],
                                ['code' => '+1664', 'name' => 'Montserrat', 'iso' => 'MS'],
                                ['code' => '+212', 'name' => 'Morocco', 'iso' => 'MA'],
                                ['code' => '+258', 'name' => 'Mozambique', 'iso' => 'MZ'],
                                ['code' => '+95', 'name' => 'Myanmar', 'iso' => 'MM'],
                                ['code' => '+264', 'name' => 'Namibia', 'iso' => 'NA'],
                                ['code' => '+674', 'name' => 'Nauru', 'iso' => 'NR'],
                                ['code' => '+977', 'name' => 'Nepal', 'iso' => 'NP'],
                                ['code' => '+31', 'name' => 'Netherlands', 'iso' => 'NL'],
                                ['code' => '+687', 'name' => 'New Caledonia', 'iso' => 'NC'],
                                ['code' => '+64', 'name' => 'New Zealand', 'iso' => 'NZ'],
                                ['code' => '+505', 'name' => 'Nicaragua', 'iso' => 'NI'],
                                ['code' => '+227', 'name' => 'Niger', 'iso' => 'NE'],
                                ['code' => '+234', 'name' => 'Nigeria', 'iso' => 'NG'],
                                ['code' => '+683', 'name' => 'Niue', 'iso' => 'NU'],
                                ['code' => '+850', 'name' => 'North Korea', 'iso' => 'KP'],
                                ['code' => '+1670', 'name' => 'Northern Mariana Islands', 'iso' => 'MP'],
                                ['code' => '+47', 'name' => 'Norway', 'iso' => 'NO'],
                                ['code' => '+968', 'name' => 'Oman', 'iso' => 'OM'],
                                ['code' => '+92', 'name' => 'Pakistan', 'iso' => 'PK'],
                                ['code' => '+680', 'name' => 'Palau', 'iso' => 'PW'],
                                ['code' => '+970', 'name' => 'Palestine', 'iso' => 'PS'],
                                ['code' => '+507', 'name' => 'Panama', 'iso' => 'PA'],
                                ['code' => '+675', 'name' => 'Papua New Guinea', 'iso' => 'PG'],
                                ['code' => '+595', 'name' => 'Paraguay', 'iso' => 'PY'],
                                ['code' => '+51', 'name' => 'Peru', 'iso' => 'PE'],
                                ['code' => '+63', 'name' => 'Philippines', 'iso' => 'PH'],
                                ['code' => '+64', 'name' => 'Pitcairn Islands', 'iso' => 'PN'],
                                ['code' => '+48', 'name' => 'Poland', 'iso' => 'PL'],
                                ['code' => '+351', 'name' => 'Portugal', 'iso' => 'PT'],
                                ['code' => '+1', 'name' => 'Puerto Rico / US territories', 'iso' => 'PR'],
                                ['code' => '+974', 'name' => 'Qatar', 'iso' => 'QA'],
                                ['code' => '+40', 'name' => 'Romania', 'iso' => 'RO'],
                                ['code' => '+7', 'name' => 'Russia / Kazakhstan', 'iso' => 'RU'],
                                ['code' => '+250', 'name' => 'Rwanda', 'iso' => 'RW'],
                                ['code' => '+685', 'name' => 'Samoa', 'iso' => 'WS'],
                                ['code' => '+378', 'name' => 'San Marino', 'iso' => 'SM'],
                                ['code' => '+239', 'name' => 'Sao Tome and Principe', 'iso' => 'ST'],
                                ['code' => '+966', 'name' => 'Saudi Arabia', 'iso' => 'SA'],
                                ['code' => '+221', 'name' => 'Senegal', 'iso' => 'SN'],
                                ['code' => '+381', 'name' => 'Serbia', 'iso' => 'RS'],
                                ['code' => '+248', 'name' => 'Seychelles', 'iso' => 'SC'],
                                ['code' => '+232', 'name' => 'Sierra Leone', 'iso' => 'SL'],
                                ['code' => '+65', 'name' => 'Singapore', 'iso' => 'SG'],
                                ['code' => '+1721', 'name' => 'Sint Maarten', 'iso' => 'SX'],
                                ['code' => '+421', 'name' => 'Slovakia', 'iso' => 'SK'],
                                ['code' => '+386', 'name' => 'Slovenia', 'iso' => 'SI'],
                                ['code' => '+677', 'name' => 'Solomon Islands', 'iso' => 'SB'],
                                ['code' => '+252', 'name' => 'Somalia', 'iso' => 'SO'],
                                ['code' => '+27', 'name' => 'South Africa', 'iso' => 'ZA'],
                                ['code' => '+211', 'name' => 'South Sudan', 'iso' => 'SS'],
                                ['code' => '+34', 'name' => 'Spain', 'iso' => 'ES'],
                                ['code' => '+94', 'name' => 'Sri Lanka', 'iso' => 'LK'],
                                ['code' => '+249', 'name' => 'Sudan', 'iso' => 'SD'],
                                ['code' => '+597', 'name' => 'Suriname', 'iso' => 'SR'],
                                ['code' => '+268', 'name' => 'Eswatini (Swaziland)', 'iso' => 'SZ'],
                                ['code' => '+46', 'name' => 'Sweden', 'iso' => 'SE'],
                                ['code' => '+41', 'name' => 'Switzerland', 'iso' => 'CH'],
                                ['code' => '+963', 'name' => 'Syria', 'iso' => 'SY'],
                                ['code' => '+886', 'name' => 'Taiwan', 'iso' => 'TW'],
                                ['code' => '+992', 'name' => 'Tajikistan', 'iso' => 'TJ'],
                                ['code' => '+255', 'name' => 'Tanzania', 'iso' => 'TZ'],
                                ['code' => '+66', 'name' => 'Thailand', 'iso' => 'TH'],
                                ['code' => '+670', 'name' => 'Timor-Leste', 'iso' => 'TL'],
                                ['code' => '+228', 'name' => 'Togo', 'iso' => 'TG'],
                                ['code' => '+690', 'name' => 'Tokelau', 'iso' => 'TK'],
                                ['code' => '+676', 'name' => 'Tonga', 'iso' => 'TO'],
                                ['code' => '+1868', 'name' => 'Trinidad and Tobago', 'iso' => 'TT'],
                                ['code' => '+216', 'name' => 'Tunisia', 'iso' => 'TN'],
                                ['code' => '+90', 'name' => 'Turkey', 'iso' => 'TR'],
                                ['code' => '+993', 'name' => 'Turkmenistan', 'iso' => 'TM'],
                                ['code' => '+1649', 'name' => 'Turks and Caicos Islands', 'iso' => 'TC'],
                                ['code' => '+688', 'name' => 'Tuvalu', 'iso' => 'TV'],
                                ['code' => '+256', 'name' => 'Uganda', 'iso' => 'UG'],
                                ['code' => '+380', 'name' => 'Ukraine', 'iso' => 'UA'],
                                ['code' => '+971', 'name' => 'United Arab Emirates', 'iso' => 'AE'],
                                ['code' => '+44', 'name' => 'United Kingdom', 'iso' => 'GB'],
                                ['code' => '+1', 'name' => 'United States', 'iso' => 'US'],
                                ['code' => '+598', 'name' => 'Uruguay', 'iso' => 'UY'],
                                ['code' => '+998', 'name' => 'Uzbekistan', 'iso' => 'UZ'],
                                ['code' => '+678', 'name' => 'Vanuatu', 'iso' => 'VU'],
                                ['code' => '+379', 'name' => 'Vatican City', 'iso' => 'VA'],
                                ['code' => '+58', 'name' => 'Venezuela', 'iso' => 'VE'],
                                ['code' => '+84', 'name' => 'Vietnam', 'iso' => 'VN'],
                                ['code' => '+681', 'name' => 'Wallis and Futuna', 'iso' => 'WF'],
                                ['code' => '+967', 'name' => 'Yemen', 'iso' => 'YE'],
                                ['code' => '+260', 'name' => 'Zambia', 'iso' => 'ZM'],
                                ['code' => '+263', 'name' => 'Zimbabwe', 'iso' => 'ZW'],
                            ];
                        @endphp
                        <div class="custom-select-wrapper" style="position:relative">
                            <button type="button" id="phone_country_toggle" class="input" style="display:flex;justify-content:space-between;align-items:center;"> 
                                <span id="phone_country_display">{{ old('phone_country_code') ?: 'Country Code' }}</span>
                                <span style="margin-left:8px">▾</span>
                            </button>
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="{{ old('phone_country_code') }}">
                            <div id="phone_country_list" class="custom-select-list" style="position:absolute;left:0;right:0;background:var(--card);border:1px solid var(--border);border-radius:10px;margin-top:8px;overflow:auto;display:none;z-index:1200;">
                                    @foreach($countryCodes as $c)
                                        <div class="custom-select-item" data-value="{{ $c['code'] }}" style="padding:10px 12px;cursor:pointer;border-bottom:1px solid rgba(0,0,0,0.04);">{!! flagFromIso($c['iso'] ?? '') !!} {{ $c['code'] }} ({{ $c['name'] }})</div>
                                    @endforeach
                                </div>
                        </div>
                        @error('phone_country_code') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="phone_number">Phone Number</label>
                        <input id="phone_number" name="phone_number" type="text" class="input" value="{{ old('phone_number') }}">
                        @error('phone_number') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="input" required>
                        <button type="button" class="toggle" id="togglePwd"><i class="ri-eye-off-line"></i></button>
                    </div>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>
                        <button type="button" class="toggle" id="togglePwd2"><i class="ri-eye-off-line"></i></button>
                    </div>
                </div>

                <button class="btn" type="submit">
                    <i class="ri-user-add-line"></i> Create Account
                </button>

                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <div class="footer-link">
                    <a href="{{ route('login') }}">Sign in to your account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if (!btn || !input) return;
            btn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                btn.querySelector('i').className = isPassword ? 'ri-eye-line' : 'ri-eye-off-line';
            });
        }
        setupToggle('togglePwd', 'password');
        setupToggle('togglePwd2', 'password_confirmation');
    })();
</script>
<script>
    // Country dropdown: show up to 10 items in the visible area and allow scrolling
    (function(){
        const toggle = document.getElementById('phone_country_toggle');
        const list = document.getElementById('phone_country_list');
        const hidden = document.getElementById('phone_country_code');
        const display = document.getElementById('phone_country_display');
        if (!toggle || !list || !hidden) return;

        // limit visible height to ~10 items (item height ~= 40px)
        const itemHeight = 40;
        list.style.maxHeight = (itemHeight * 10) + 'px';

        toggle.addEventListener('click', function(e){
            e.stopPropagation();
            const shown = list.style.display === 'block';
            list.style.display = shown ? 'none' : 'block';
        });

        // select an item
        list.addEventListener('click', function(e){
            const it = e.target.closest('.custom-select-item');
            if (!it) return;
            const val = it.getAttribute('data-value');
            hidden.value = val;
            display.textContent = val + ' ' + it.textContent.replace(val, '').trim();
            list.style.display = 'none';
        });

        // close on outside click
        document.addEventListener('click', function(){ list.style.display = 'none'; });

        // restore selection highlight on load
        (function restore(){
            const cur = hidden.value;
            if (!cur) return;
            const sel = list.querySelector('[data-value="'+cur+'"]');
            if (sel) {
                // scroll into view
                sel.scrollIntoView({block:'center'});
                display.textContent = cur + ' ' + sel.textContent.replace(cur, '').trim();
            }
        })();
    })();
</script>
</body>
</html>