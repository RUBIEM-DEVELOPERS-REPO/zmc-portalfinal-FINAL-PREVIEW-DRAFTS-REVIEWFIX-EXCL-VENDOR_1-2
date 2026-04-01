@props(['name' => 'phone_country_code', 'id' => null, 'value' => '', 'required' => false, 'class' => 'form-control'])

<select 
    name="{{ $name }}" 
    id="{{ $id ?? $name }}" 
    class="{{ $class }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes }}
>
    <option value="">Select Country Code</option>
    <option value="+93" {{ $value == '+93' ? 'selected' : '' }}>🇦🇫 +93 (Afghanistan)</option>
    <option value="+355" {{ $value == '+355' ? 'selected' : '' }}>🇦🇱 +355 (Albania)</option>
    <option value="+213" {{ $value == '+213' ? 'selected' : '' }}>🇩🇿 +213 (Algeria)</option>
    <option value="+376" {{ $value == '+376' ? 'selected' : '' }}>🇦🇩 +376 (Andorra)</option>
    <option value="+244" {{ $value == '+244' ? 'selected' : '' }}>🇦🇴 +244 (Angola)</option>
    <option value="+54" {{ $value == '+54' ? 'selected' : '' }}>🇦🇷 +54 (Argentina)</option>
    <option value="+374" {{ $value == '+374' ? 'selected' : '' }}>🇦🇲 +374 (Armenia)</option>
    <option value="+61" {{ $value == '+61' ? 'selected' : '' }}>🇦🇺 +61 (Australia)</option>
    <option value="+43" {{ $value == '+43' ? 'selected' : '' }}>🇦🇹 +43 (Austria)</option>
    <option value="+994" {{ $value == '+994' ? 'selected' : '' }}>🇦🇿 +994 (Azerbaijan)</option>
    <option value="+973" {{ $value == '+973' ? 'selected' : '' }}>🇧🇭 +973 (Bahrain)</option>
    <option value="+880" {{ $value == '+880' ? 'selected' : '' }}>🇧🇩 +880 (Bangladesh)</option>
    <option value="+375" {{ $value == '+375' ? 'selected' : '' }}>🇧🇾 +375 (Belarus)</option>
    <option value="+32" {{ $value == '+32' ? 'selected' : '' }}>🇧🇪 +32 (Belgium)</option>
    <option value="+501" {{ $value == '+501' ? 'selected' : '' }}>🇧🇿 +501 (Belize)</option>
    <option value="+229" {{ $value == '+229' ? 'selected' : '' }}>🇧🇯 +229 (Benin)</option>
    <option value="+975" {{ $value == '+975' ? 'selected' : '' }}>🇧🇹 +975 (Bhutan)</option>
    <option value="+591" {{ $value == '+591' ? 'selected' : '' }}>🇧🇴 +591 (Bolivia)</option>
    <option value="+387" {{ $value == '+387' ? 'selected' : '' }}>🇧🇦 +387 (Bosnia and Herzegovina)</option>
    <option value="+267" {{ $value == '+267' ? 'selected' : '' }}>🇧🇼 +267 (Botswana)</option>
    <option value="+55" {{ $value == '+55' ? 'selected' : '' }}>🇧🇷 +55 (Brazil)</option>
    <option value="+673" {{ $value == '+673' ? 'selected' : '' }}>🇧🇳 +673 (Brunei)</option>
    <option value="+359" {{ $value == '+359' ? 'selected' : '' }}>🇧🇬 +359 (Bulgaria)</option>
    <option value="+226" {{ $value == '+226' ? 'selected' : '' }}>🇧🇫 +226 (Burkina Faso)</option>
    <option value="+257" {{ $value == '+257' ? 'selected' : '' }}>🇧🇮 +257 (Burundi)</option>
    <option value="+855" {{ $value == '+855' ? 'selected' : '' }}>🇰🇭 +855 (Cambodia)</option>
    <option value="+237" {{ $value == '+237' ? 'selected' : '' }}>🇨🇲 +237 (Cameroon)</option>
    <option value="+1" {{ $value == '+1' ? 'selected' : '' }}>🇨🇦 +1 (Canada)</option>
    <option value="+238" {{ $value == '+238' ? 'selected' : '' }}>🇨🇻 +238 (Cape Verde)</option>
    <option value="+236" {{ $value == '+236' ? 'selected' : '' }}>🇨🇫 +236 (Central African Republic)</option>
    <option value="+235" {{ $value == '+235' ? 'selected' : '' }}>🇹🇩 +235 (Chad)</option>
    <option value="+56" {{ $value == '+56' ? 'selected' : '' }}>🇨🇱 +56 (Chile)</option>
    <option value="+86" {{ $value == '+86' ? 'selected' : '' }}>🇨🇳 +86 (China)</option>
    <option value="+57" {{ $value == '+57' ? 'selected' : '' }}>🇨🇴 +57 (Colombia)</option>
    <option value="+269" {{ $value == '+269' ? 'selected' : '' }}>🇰🇲 +269 (Comoros)</option>
    <option value="+242" {{ $value == '+242' ? 'selected' : '' }}>🇨🇬 +242 (Congo)</option>
    <option value="+243" {{ $value == '+243' ? 'selected' : '' }}>🇨🇩 +243 (Congo, Democratic Republic)</option>
    <option value="+506" {{ $value == '+506' ? 'selected' : '' }}>🇨🇷 +506 (Costa Rica)</option>
    <option value="+385" {{ $value == '+385' ? 'selected' : '' }}>🇭🇷 +385 (Croatia)</option>
    <option value="+53" {{ $value == '+53' ? 'selected' : '' }}>🇨🇺 +53 (Cuba)</option>
    <option value="+357" {{ $value == '+357' ? 'selected' : '' }}>🇨🇾 +357 (Cyprus)</option>
    <option value="+420" {{ $value == '+420' ? 'selected' : '' }}>🇨🇿 +420 (Czech Republic)</option>
    <option value="+45" {{ $value == '+45' ? 'selected' : '' }}>🇩🇰 +45 (Denmark)</option>
    <option value="+253" {{ $value == '+253' ? 'selected' : '' }}>🇩🇯 +253 (Djibouti)</option>
    <option value="+593" {{ $value == '+593' ? 'selected' : '' }}>🇪🇨 +593 (Ecuador)</option>
    <option value="+20" {{ $value == '+20' ? 'selected' : '' }}>🇪🇬 +20 (Egypt)</option>
    <option value="+503" {{ $value == '+503' ? 'selected' : '' }}>🇸🇻 +503 (El Salvador)</option>
    <option value="+240" {{ $value == '+240' ? 'selected' : '' }}>🇬🇶 +240 (Equatorial Guinea)</option>
    <option value="+291" {{ $value == '+291' ? 'selected' : '' }}>🇪🇷 +291 (Eritrea)</option>
    <option value="+372" {{ $value == '+372' ? 'selected' : '' }}>🇪🇪 +372 (Estonia)</option>
    <option value="+268" {{ $value == '+268' ? 'selected' : '' }}>🇸🇿 +268 (Eswatini)</option>
    <option value="+251" {{ $value == '+251' ? 'selected' : '' }}>🇪🇹 +251 (Ethiopia)</option>
    <option value="+679" {{ $value == '+679' ? 'selected' : '' }}>🇫🇯 +679 (Fiji)</option>
    <option value="+358" {{ $value == '+358' ? 'selected' : '' }}>🇫🇮 +358 (Finland)</option>
    <option value="+33" {{ $value == '+33' ? 'selected' : '' }}>🇫🇷 +33 (France)</option>
    <option value="+241" {{ $value == '+241' ? 'selected' : '' }}>🇬🇦 +241 (Gabon)</option>
    <option value="+220" {{ $value == '+220' ? 'selected' : '' }}>🇬🇲 +220 (Gambia)</option>
    <option value="+995" {{ $value == '+995' ? 'selected' : '' }}>🇬🇪 +995 (Georgia)</option>
    <option value="+49" {{ $value == '+49' ? 'selected' : '' }}>🇩🇪 +49 (Germany)</option>
    <option value="+233" {{ $value == '+233' ? 'selected' : '' }}>🇬🇭 +233 (Ghana)</option>
    <option value="+30" {{ $value == '+30' ? 'selected' : '' }}>🇬🇷 +30 (Greece)</option>
    <option value="+502" {{ $value == '+502' ? 'selected' : '' }}>🇬🇹 +502 (Guatemala)</option>
    <option value="+224" {{ $value == '+224' ? 'selected' : '' }}>🇬🇳 +224 (Guinea)</option>
    <option value="+245" {{ $value == '+245' ? 'selected' : '' }}>🇬🇼 +245 (Guinea-Bissau)</option>
    <option value="+592" {{ $value == '+592' ? 'selected' : '' }}>🇬🇾 +592 (Guyana)</option>
    <option value="+509" {{ $value == '+509' ? 'selected' : '' }}>🇭🇹 +509 (Haiti)</option>
    <option value="+504" {{ $value == '+504' ? 'selected' : '' }}>🇭🇳 +504 (Honduras)</option>
    <option value="+852" {{ $value == '+852' ? 'selected' : '' }}>🇭🇰 +852 (Hong Kong)</option>
    <option value="+36" {{ $value == '+36' ? 'selected' : '' }}>🇭🇺 +36 (Hungary)</option>
    <option value="+354" {{ $value == '+354' ? 'selected' : '' }}>🇮🇸 +354 (Iceland)</option>
    <option value="+91" {{ $value == '+91' ? 'selected' : '' }}>🇮🇳 +91 (India)</option>
    <option value="+62" {{ $value == '+62' ? 'selected' : '' }}>🇮🇩 +62 (Indonesia)</option>
    <option value="+98" {{ $value == '+98' ? 'selected' : '' }}>🇮🇷 +98 (Iran)</option>
    <option value="+964" {{ $value == '+964' ? 'selected' : '' }}>🇮🇶 +964 (Iraq)</option>
    <option value="+353" {{ $value == '+353' ? 'selected' : '' }}>🇮🇪 +353 (Ireland)</option>
    <option value="+972" {{ $value == '+972' ? 'selected' : '' }}>🇮🇱 +972 (Israel)</option>
    <option value="+39" {{ $value == '+39' ? 'selected' : '' }}>🇮🇹 +39 (Italy)</option>
    <option value="+225" {{ $value == '+225' ? 'selected' : '' }}>🇨🇮 +225 (Ivory Coast)</option>
    <option value="+81" {{ $value == '+81' ? 'selected' : '' }}>🇯🇵 +81 (Japan)</option>
    <option value="+962" {{ $value == '+962' ? 'selected' : '' }}>🇯🇴 +962 (Jordan)</option>
    <option value="+7" {{ $value == '+7' ? 'selected' : '' }}>🇰🇿 +7 (Kazakhstan)</option>
    <option value="+254" {{ $value == '+254' ? 'selected' : '' }}>🇰🇪 +254 (Kenya)</option>
    <option value="+965" {{ $value == '+965' ? 'selected' : '' }}>🇰🇼 +965 (Kuwait)</option>
    <option value="+996" {{ $value == '+996' ? 'selected' : '' }}>🇰🇬 +996 (Kyrgyzstan)</option>
    <option value="+856" {{ $value == '+856' ? 'selected' : '' }}>🇱🇦 +856 (Laos)</option>
    <option value="+371" {{ $value == '+371' ? 'selected' : '' }}>🇱🇻 +371 (Latvia)</option>
    <option value="+961" {{ $value == '+961' ? 'selected' : '' }}>🇱🇧 +961 (Lebanon)</option>
    <option value="+266" {{ $value == '+266' ? 'selected' : '' }}>🇱🇸 +266 (Lesotho)</option>
    <option value="+231" {{ $value == '+231' ? 'selected' : '' }}>🇱🇷 +231 (Liberia)</option>
    <option value="+218" {{ $value == '+218' ? 'selected' : '' }}>🇱🇾 +218 (Libya)</option>
    <option value="+370" {{ $value == '+370' ? 'selected' : '' }}>🇱🇹 +370 (Lithuania)</option>
    <option value="+352" {{ $value == '+352' ? 'selected' : '' }}>🇱🇺 +352 (Luxembourg)</option>
    <option value="+261" {{ $value == '+261' ? 'selected' : '' }}>🇲🇬 +261 (Madagascar)</option>
    <option value="+265" {{ $value == '+265' ? 'selected' : '' }}>🇲🇼 +265 (Malawi)</option>
    <option value="+60" {{ $value == '+60' ? 'selected' : '' }}>🇲🇾 +60 (Malaysia)</option>
    <option value="+960" {{ $value == '+960' ? 'selected' : '' }}>🇲🇻 +960 (Maldives)</option>
    <option value="+223" {{ $value == '+223' ? 'selected' : '' }}>🇲🇱 +223 (Mali)</option>
    <option value="+356" {{ $value == '+356' ? 'selected' : '' }}>🇲🇹 +356 (Malta)</option>
    <option value="+222" {{ $value == '+222' ? 'selected' : '' }}>🇲🇷 +222 (Mauritania)</option>
    <option value="+230" {{ $value == '+230' ? 'selected' : '' }}>🇲🇺 +230 (Mauritius)</option>
    <option value="+52" {{ $value == '+52' ? 'selected' : '' }}>🇲🇽 +52 (Mexico)</option>
    <option value="+373" {{ $value == '+373' ? 'selected' : '' }}>🇲🇩 +373 (Moldova)</option>
    <option value="+377" {{ $value == '+377' ? 'selected' : '' }}>🇲🇨 +377 (Monaco)</option>
    <option value="+976" {{ $value == '+976' ? 'selected' : '' }}>🇲🇳 +976 (Mongolia)</option>
    <option value="+382" {{ $value == '+382' ? 'selected' : '' }}>🇲🇪 +382 (Montenegro)</option>
    <option value="+212" {{ $value == '+212' ? 'selected' : '' }}>🇲🇦 +212 (Morocco)</option>
    <option value="+258" {{ $value == '+258' ? 'selected' : '' }}>🇲🇿 +258 (Mozambique)</option>
    <option value="+95" {{ $value == '+95' ? 'selected' : '' }}>🇲🇲 +95 (Myanmar)</option>
    <option value="+264" {{ $value == '+264' ? 'selected' : '' }}>🇳🇦 +264 (Namibia)</option>
    <option value="+977" {{ $value == '+977' ? 'selected' : '' }}>🇳🇵 +977 (Nepal)</option>
    <option value="+31" {{ $value == '+31' ? 'selected' : '' }}>🇳🇱 +31 (Netherlands)</option>
    <option value="+64" {{ $value == '+64' ? 'selected' : '' }}>🇳🇿 +64 (New Zealand)</option>
    <option value="+505" {{ $value == '+505' ? 'selected' : '' }}>🇳🇮 +505 (Nicaragua)</option>
    <option value="+227" {{ $value == '+227' ? 'selected' : '' }}>🇳🇪 +227 (Niger)</option>
    <option value="+234" {{ $value == '+234' ? 'selected' : '' }}>🇳🇬 +234 (Nigeria)</option>
    <option value="+850" {{ $value == '+850' ? 'selected' : '' }}>🇰🇵 +850 (North Korea)</option>
    <option value="+389" {{ $value == '+389' ? 'selected' : '' }}>🇲🇰 +389 (North Macedonia)</option>
    <option value="+47" {{ $value == '+47' ? 'selected' : '' }}>🇳🇴 +47 (Norway)</option>
    <option value="+968" {{ $value == '+968' ? 'selected' : '' }}>🇴🇲 +968 (Oman)</option>
    <option value="+92" {{ $value == '+92' ? 'selected' : '' }}>🇵🇰 +92 (Pakistan)</option>
    <option value="+970" {{ $value == '+970' ? 'selected' : '' }}>🇵🇸 +970 (Palestine)</option>
    <option value="+507" {{ $value == '+507' ? 'selected' : '' }}>🇵🇦 +507 (Panama)</option>
    <option value="+675" {{ $value == '+675' ? 'selected' : '' }}>🇵🇬 +675 (Papua New Guinea)</option>
    <option value="+595" {{ $value == '+595' ? 'selected' : '' }}>🇵🇾 +595 (Paraguay)</option>
    <option value="+51" {{ $value == '+51' ? 'selected' : '' }}>🇵🇪 +51 (Peru)</option>
    <option value="+63" {{ $value == '+63' ? 'selected' : '' }}>🇵🇭 +63 (Philippines)</option>
    <option value="+48" {{ $value == '+48' ? 'selected' : '' }}>🇵🇱 +48 (Poland)</option>
    <option value="+351" {{ $value == '+351' ? 'selected' : '' }}>🇵🇹 +351 (Portugal)</option>
    <option value="+974" {{ $value == '+974' ? 'selected' : '' }}>🇶🇦 +974 (Qatar)</option>
    <option value="+40" {{ $value == '+40' ? 'selected' : '' }}>🇷🇴 +40 (Romania)</option>
    <option value="+7" {{ $value == '+7' ? 'selected' : '' }}>🇷🇺 +7 (Russia)</option>
    <option value="+250" {{ $value == '+250' ? 'selected' : '' }}>🇷🇼 +250 (Rwanda)</option>
    <option value="+966" {{ $value == '+966' ? 'selected' : '' }}>🇸🇦 +966 (Saudi Arabia)</option>
    <option value="+221" {{ $value == '+221' ? 'selected' : '' }}>🇸🇳 +221 (Senegal)</option>
    <option value="+381" {{ $value == '+381' ? 'selected' : '' }}>🇷🇸 +381 (Serbia)</option>
    <option value="+248" {{ $value == '+248' ? 'selected' : '' }}>🇸🇨 +248 (Seychelles)</option>
    <option value="+232" {{ $value == '+232' ? 'selected' : '' }}>🇸🇱 +232 (Sierra Leone)</option>
    <option value="+65" {{ $value == '+65' ? 'selected' : '' }}>🇸🇬 +65 (Singapore)</option>
    <option value="+421" {{ $value == '+421' ? 'selected' : '' }}>🇸🇰 +421 (Slovakia)</option>
    <option value="+386" {{ $value == '+386' ? 'selected' : '' }}>🇸🇮 +386 (Slovenia)</option>
    <option value="+677" {{ $value == '+677' ? 'selected' : '' }}>🇸🇧 +677 (Solomon Islands)</option>
    <option value="+252" {{ $value == '+252' ? 'selected' : '' }}>🇸🇴 +252 (Somalia)</option>
    <option value="+27" {{ $value == '+27' ? 'selected' : '' }}>🇿🇦 +27 (South Africa)</option>
    <option value="+82" {{ $value == '+82' ? 'selected' : '' }}>🇰🇷 +82 (South Korea)</option>
    <option value="+211" {{ $value == '+211' ? 'selected' : '' }}>🇸🇸 +211 (South Sudan)</option>
    <option value="+34" {{ $value == '+34' ? 'selected' : '' }}>🇪🇸 +34 (Spain)</option>
    <option value="+94" {{ $value == '+94' ? 'selected' : '' }}>🇱🇰 +94 (Sri Lanka)</option>
    <option value="+249" {{ $value == '+249' ? 'selected' : '' }}>🇸🇩 +249 (Sudan)</option>
    <option value="+597" {{ $value == '+597' ? 'selected' : '' }}>🇸🇷 +597 (Suriname)</option>
    <option value="+46" {{ $value == '+46' ? 'selected' : '' }}>🇸🇪 +46 (Sweden)</option>
    <option value="+41" {{ $value == '+41' ? 'selected' : '' }}>🇨🇭 +41 (Switzerland)</option>
    <option value="+963" {{ $value == '+963' ? 'selected' : '' }}>🇸🇾 +963 (Syria)</option>
    <option value="+886" {{ $value == '+886' ? 'selected' : '' }}>🇹🇼 +886 (Taiwan)</option>
    <option value="+992" {{ $value == '+992' ? 'selected' : '' }}>🇹🇯 +992 (Tajikistan)</option>
    <option value="+255" {{ $value == '+255' ? 'selected' : '' }}>🇹🇿 +255 (Tanzania)</option>
    <option value="+66" {{ $value == '+66' ? 'selected' : '' }}>🇹🇭 +66 (Thailand)</option>
    <option value="+228" {{ $value == '+228' ? 'selected' : '' }}>🇹🇬 +228 (Togo)</option>
    <option value="+216" {{ $value == '+216' ? 'selected' : '' }}>🇹🇳 +216 (Tunisia)</option>
    <option value="+90" {{ $value == '+90' ? 'selected' : '' }}>🇹🇷 +90 (Turkey)</option>
    <option value="+993" {{ $value == '+993' ? 'selected' : '' }}>🇹🇲 +993 (Turkmenistan)</option>
    <option value="+256" {{ $value == '+256' ? 'selected' : '' }}>🇺🇬 +256 (Uganda)</option>
    <option value="+380" {{ $value == '+380' ? 'selected' : '' }}>🇺🇦 +380 (Ukraine)</option>
    <option value="+971" {{ $value == '+971' ? 'selected' : '' }}>🇦🇪 +971 (United Arab Emirates)</option>
    <option value="+44" {{ $value == '+44' ? 'selected' : '' }}>🇬🇧 +44 (United Kingdom)</option>
    <option value="+1" {{ $value == '+1' ? 'selected' : '' }}>🇺🇸 +1 (United States)</option>
    <option value="+598" {{ $value == '+598' ? 'selected' : '' }}>🇺🇾 +598 (Uruguay)</option>
    <option value="+998" {{ $value == '+998' ? 'selected' : '' }}>🇺🇿 +998 (Uzbekistan)</option>
    <option value="+58" {{ $value == '+58' ? 'selected' : '' }}>🇻🇪 +58 (Venezuela)</option>
    <option value="+84" {{ $value == '+84' ? 'selected' : '' }}>🇻🇳 +84 (Vietnam)</option>
    <option value="+967" {{ $value == '+967' ? 'selected' : '' }}>🇾🇪 +967 (Yemen)</option>
    <option value="+260" {{ $value == '+260' ? 'selected' : '' }}>🇿🇲 +260 (Zambia)</option>
    <option value="+263" {{ $value == '+263' ? 'selected' : '' }}>🇿🇼 +263 (Zimbabwe)</option>
</select>
