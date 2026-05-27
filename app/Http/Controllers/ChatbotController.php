<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    private $knowledgeBase = [
        'about_zmc' => [
            'keywords' => ['zmc', 'zimbabwe media commission', 'what is zmc', 'about zmc', 'commission', 'who are you', 'tell me about'],
            'response' => "The Zimbabwe Media Commission (ZMC) is the statutory body established under Section 248 of the Constitution of Zimbabwe to uphold, promote, and develop freedom of the media.\n\n**Our Mandate:**\n- Register and accredit media practitioners\n- Register mass media services\n- Promote and enforce good practices in the media\n- Ensure fair competition among media houses\n- Encourage the development of media training and technology\n\n**Contact:**\n- Address: ZMC House, 108 Swan Drive, Alexandra Park, Harare\n- Phone: 253509/10 or 253572/5/6\n- Email: zmcaccreditation@gmail.com\n- Website: www.zmc.org.zw"
        ],
        'accreditation_overview' => [
            'keywords' => ['accreditation', 'accredit', 'journalist', 'press card', 'how to become', 'become journalist', 'ap3', 'get accredited', 'media card', 'journalist card'],
            'response' => "**Media Practitioner Accreditation (AP3 Form)**\n\nTo practice media work in Zimbabwe, you need to be accredited by ZMC. Here's how:\n\n**Steps:**\n1. Create an account on our portal\n2. Complete the AP3 application form\n3. Upload required documents\n4. Pay the application fee\n5. Await verification and approval\n6. Collect your press card from your chosen regional office\n\n**Types of Accreditation:**\n- **Local Media Practitioners** - Zimbabwean citizens working for local media\n- **Foreign Media Practitioners** - International media practitioners\n\n**Fees:**\n- Local Media Practitioner: USD $50\n- Foreign Media Practitioner: USD $200\n\nType 'documents for accreditation' to see required documents."
        ],
        'accreditation_documents' => [
            'keywords' => ['documents accreditation', 'documents journalist', 'accreditation documents', 'ap3 documents', 'what documents press card', 'requirements accreditation', 'what do i need', 'required documents'],
            'response' => "**Required Documents for Media Practitioner Accreditation (AP3):**\n\n**For Local Media Practitioners:**\n1. Certified copy of National ID\n2. Passport-size photograph (colored, recent)\n3. Proof of employment from media house\n4. Educational certificates (journalism/mass communication preferred)\n5. CV/Resume\n6. Police clearance certificate\n\n**For Foreign Media Practitioners:**\n1. Valid passport (certified copy)\n2. Passport-size photograph (colored, recent)\n3. Letter of assignment from media organization\n4. Immigration/work permit documents\n5. CV/Resume\n6. Police clearance from country of origin\n\n**File Requirements:**\n- PDF or image format (JPEG, PNG)\n- Maximum 5MB per file\n- Clear and legible copies"
        ],
        'registration_overview' => [
            'keywords' => ['register', 'registration', 'media house', 'ap1', 'register media', 'start media', 'media company', 'broadcast', 'newspaper', 'online media', 'radio', 'television', 'tv station'],
            'response' => "**Media House Registration (AP1 Form)**\n\nAll mass media services in Zimbabwe must be registered with ZMC. Here's the process:\n\n**Steps:**\n1. Create an account on our portal\n2. Complete the AP1 registration form\n3. Upload required annexures (supporting documents)\n4. Pay the registration fee\n5. Await verification and site inspection\n6. Receive your registration certificate\n\n**Categories:**\n- Print Media (newspapers, magazines)\n- Broadcasting (radio, television)\n- Online/Digital Media\n- News Agencies\n\n**Fees:**\n- Registration: USD $500 - $2,000 (varies by category)\n- Annual renewal: 50% of registration fee\n\nType 'documents for registration' to see required annexures."
        ],
        'registration_documents' => [
            'keywords' => ['documents registration', 'documents media house', 'registration documents', 'ap1 documents', 'annexures', 'requirements registration', 'what documents register', 'media house requirements'],
            'response' => "**Required Annexures for Media House Registration (AP1):**\n\n**Annexure A - Company Documents:**\n- Certificate of Incorporation (CR14)\n- Memorandum and Articles of Association\n- Tax clearance certificate (ZIMRA)\n\n**Annexure B - Ownership Declaration:**\n- Directors' certified ID copies\n- Shareholding structure\n- Declaration of beneficial ownership\n\n**Annexure C - Operational Plan:**\n- Business plan/proposal\n- Editorial policy document\n- Code of conduct\n\n**Annexure D - Technical Requirements:**\n- For broadcasters: Frequency allocation letter (POTRAZ)\n- Studio/office location details\n- Equipment specifications\n\n**Annexure E - Financial Capacity:**\n- Bank statements (3 months)\n- Proof of capital\n- Financial projections\n\n**File Requirements:**\n- PDF format preferred\n- Maximum 10MB per file"
        ],
        'fees' => [
            'keywords' => ['fees', 'cost', 'how much', 'price', 'payment', 'pay', 'charges', 'money', 'amount'],
            'response' => "**ZMC Fees Schedule:**\n\n**Media Practitioner Accreditation:**\n- Local Media Practitioner: USD $50\n- Foreign Media Practitioner: USD $200\n- Press Card Replacement: USD $20\n- Renewal: Same as initial fee\n\n**Media House Registration:**\n- Print Media: USD $500 - $1,000\n- Broadcasting: USD $1,000 - $2,000\n- Online Media: USD $300 - $500\n- News Agency: USD $500\n\n**Payment Methods:**\n- Bank transfer\n- Mobile money (EcoCash, OneMoney)\n- Paynow online payment\n- Cash at ZMC offices\n\n**Bank Details:**\n- Bank: CBZ Bank\n- Account: Zimbabwe Media Commission\n- Branch: Harare\n\nFor specific fee inquiries, email: zmcaccreditation@gmail.com"
        ],
        'paynow' => [
            'keywords' => ['paynow', 'ecocash', 'onemoney', 'mobile money', 'online payment', 'pay online', 'mobile payment'],
            'response' => "**Paynow Payment Guide:**\n\n**How to Pay via Paynow:**\n1. Select 'Pay Now' on your application dashboard\n2. Choose your preferred payment method:\n   - EcoCash\n   - OneMoney\n   - Bank Card (Visa/MasterCard)\n3. Enter your payment details\n4. Confirm the transaction\n5. Wait for payment confirmation\n\n**After Payment:**\n- Your payment will be verified within 24 hours\n- You'll receive an email/SMS confirmation\n- Your application status will update to 'Paid'\n\n**Payment Issues?**\n- Keep your transaction reference number\n- Contact: zmcaccreditation@gmail.com\n- Phone: 253509/10 or 253572/5/6\n\n**Note:** Processing may take up to 48 hours for bank transfers."
        ],
        'offices' => [
            'keywords' => ['office', 'location', 'where', 'address', 'collect', 'regional', 'harare', 'bulawayo', 'mutare', 'masvingo', 'pick up', 'collection point', 'gweru', 'chinhoyi'],
            'response' => "**ZMC Regional Offices:**\n\n**Head Office - Harare:**\n- Address: ZMC House, 108 Swan Drive, Alexandra Park, Harare\n- Phone: 253509/10 or 253572/5/6\n- Hours: Mon-Fri 8:00 AM - 4:30 PM\n\n**Bulawayo Regional Office:**\n- Address: Room 12, CABS Centre, 74 Jason Moyo Street\n- Phone: +263 292 123 456\n\n**Mutare Regional Office:**\n- Address: 2nd Floor, Old Mutual Building, Main Street\n- Phone: +263 202 654 321\n\n**Masvingo Regional Office:**\n- Address: Suite 5, TelOne Complex, Robert Mugabe Way\n- Phone: +263 392 234 567\n\n**Gweru Regional Office:**\n- Address: 1st Floor, NRZ Building, 6th Street\n- Phone: +263 254 567 890\n\n**Services at Regional Offices:**\n- Press card collection\n- Certificate collection\n- Application assistance\n- Document verification"
        ],
        'track' => [
            'keywords' => ['track', 'status', 'application status', 'check', 'progress', 'where is my', 'how long', 'when will', 'waiting', 'processing time'],
            'response' => "**Track Your Application:**\n\nYou can track your application status through:\n\n1. **Online Portal:**\n   - Log in to your account\n   - Go to Dashboard\n   - View 'My Applications' section\n\n2. **Application Statuses:**\n   - **Draft** - Not yet submitted\n   - **Submitted** - Received, awaiting review\n   - **Under Review** - Being processed by officer\n   - **Pending Payment** - Approved, awaiting fee\n   - **Paid** - Payment confirmed\n   - **Approved** - Processing complete\n   - **Ready for Collection** - Card/Certificate ready\n   - **Issued** - Collected\n\n**Processing Times:**\n- Media Practitioner Accreditation: 5-10 working days\n- Media House Registration: 15-30 working days\n\nFor urgent inquiries: zmcaccreditation@gmail.com"
        ],
        'renewal' => [
            'keywords' => ['renew', 'renewal', 'expire', 'expired', 'extend', 'ap5', 'validity', 'expiring', 'renewing'],
            'response' => "**Renewal Information:**\n\n**Press Card Renewal (AP5):**\n- Cards are valid for 2 years\n- Apply for renewal 30 days before expiry\n- Same fee as initial accreditation\n- Submit updated documents if changed\n\n**Media House Registration Renewal:**\n- Annual renewal required\n- Renewal fee: 50% of initial registration\n- Submit annual compliance report\n- Updated company documents if changed\n\n**Late Renewal:**\n- Grace period: 30 days after expiry\n- Penalty: 10% per month\n- After 6 months: Re-application required\n\nTo renew, log in to your portal and select 'Renew Application' from your dashboard."
        ],
        'replacement' => [
            'keywords' => ['replacement', 'lost', 'stolen', 'damaged', 'new card', 'replace card', 'misplaced'],
            'response' => "**Card/Certificate Replacement:**\n\n**Press Card Replacement:**\n- Fee: USD $20\n- Required: Affidavit (if lost/stolen)\n- Processing time: 3-5 working days\n\n**How to Apply for Replacement:**\n1. Log in to your portal\n2. Go to 'Renewals/Replacement' section\n3. Select reason for replacement\n4. Upload police report (if stolen)\n5. Pay replacement fee\n6. Collect new card from regional office\n\n**Certificate Replacement:**\n- Fee: USD $50\n- Required: Sworn affidavit\n- Must provide original certificate details\n\nFor assistance, contact: zmcaccreditation@gmail.com"
        ],
        'contact' => [
            'keywords' => ['contact', 'email', 'phone', 'call', 'reach', 'talk', 'human', 'help', 'support', 'speak', 'complaint', 'complain'],
            'response' => "**Contact Zimbabwe Media Commission:**\n\n**General Inquiries:**\n- Email: zmcaccreditation@gmail.com\n- Phone: 253509/10 or 253572/5/6\n\n**Specific Departments:**\n- Accreditation: zmcaccreditation@gmail.com\n- Registration: zmcaccreditation@gmail.com\n- Accounts/Payments: zmcaccreditation@gmail.com\n- Complaints: zmcaccreditation@gmail.com\n\n**Office Hours:**\n- Monday to Friday: 8:00 AM - 4:30 PM\n- Closed on weekends and public holidays\n\n**Social Media:**\n- Twitter: @ZMCZimbabwe\n- Facebook: Zimbabwe Media Commission\n\nFor immediate assistance, please email zmcaccreditation@gmail.com with your query and application reference number."
        ],
        'faq_time' => [
            'keywords' => ['how long does it take', 'processing time', 'turnaround', 'when ready', 'wait time'],
            'response' => "**Processing Times FAQ:**\n\n**Q: How long does media practitioner accreditation take?**\nA: 5-10 working days from submission of complete application.\n\n**Q: How long for media house registration?**\nA: 15-30 working days, including site inspection.\n\n**Q: Why is my application taking longer?**\nPossible reasons:\n- Incomplete documents\n- Payment not yet confirmed\n- High volume of applications\n- Additional verification required\n\n**Q: Can I expedite my application?**\nA: Urgent processing is available in special cases. Contact zmcaccreditation@gmail.com with your reasons.\n\n**Q: How do I know when it's ready?**\nA: You'll receive SMS and email notification. Also check your portal dashboard."
        ],
        'faq_eligibility' => [
            'keywords' => ['eligible', 'qualify', 'can i apply', 'requirements to apply', 'who can apply', 'qualifications'],
            'response' => "**Eligibility FAQ:**\n\n**Q: Who can apply for media practitioner accreditation?**\nA: Any person practicing or intending to practice media work in Zimbabwe, including:\n- Full-time media practitioners\n- Freelance media practitioners\n- Photographers and videographers\n- Foreign correspondents\n\n**Q: Do I need a journalism degree?**\nA: Not mandatory, but relevant qualifications are considered. Experience in media is also accepted.\n\n**Q: Can a student apply?**\nA: Students on internship can apply with a letter from their institution and media house.\n\n**Q: Who can register a media house?**\nA: Any registered company or individual intending to operate a mass media service in Zimbabwe.\n\nFor more info, email: zmcaccreditation@gmail.com"
        ],
        'faq_changes' => [
            'keywords' => ['change', 'update', 'modify', 'edit', 'correct', 'wrong information', 'mistake', 'amendment'],
            'response' => "**Changes & Amendments FAQ:**\n\n**Q: Can I update my information after submission?**\nA: Contact our office to request amendments before approval.\n\n**Q: How do I change my employer?**\nA: Submit a change of employer form with new employment letter.\n\n**Q: What if my name changed?**\nA: Provide legal documentation (marriage certificate, deed poll) and apply for card replacement.\n\n**Q: Can I change my collection point?**\nA: Yes, contact us before card production to change regional office.\n\n**Q: How do I update company details?**\nA: Submit updated company documents and request amendment.\n\nFor all changes, email: zmcaccreditation@gmail.com with your reference number."
        ],
        'greeting' => [
            'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'greetings', 'howdy'],
            'response' => "Hello! Welcome to the ZMC Assistant. I can help you with:\n\n- **Media Practitioner Accreditation** - How to get your press card\n- **Media House Registration** - How to register your media company\n- **Required Documents** - What you need to submit\n- **Fees & Payments** - Costs and payment methods\n- **Office Locations** - Where to collect your card/certificate\n- **Application Tracking** - Check your application status\n- **Renewals & Replacements** - Extend validity or replace cards\n\nJust type your question or select a topic above!"
        ],
        'thanks' => [
            'keywords' => ['thank', 'thanks', 'thank you', 'appreciate', 'helpful', 'great'],
            'response' => "You're welcome! I'm glad I could help.\n\nIf you have any more questions about:\n- Media practitioner accreditation\n- Media house registration\n- Fees and payments\n- Application status\n\nFeel free to ask anytime!\n\n**Need further assistance?**\nEmail: zmcaccreditation@gmail.com\nPhone: 253509/10 or 253572/5/6"
        ],
    ];

    public function message(Request $request)
    {
        $userMessage = strtolower(trim($request->input('message', '')));
        
        if (empty($userMessage)) {
            return response()->json([
                'response' => "Please type a question and I'll do my best to help you.",
                'matched' => false
            ]);
        }

        $bestMatch = null;
        $highestScore = 0;

        foreach ($this->knowledgeBase as $topic => $data) {
            $score = 0;
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($userMessage, strtolower($keyword))) {
                    $score += strlen($keyword);
                }
            }
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $data['response'];
            }
        }

        if ($bestMatch && $highestScore >= 3) {
            return response()->json([
                'response' => $bestMatch,
                'matched' => true
            ]);
        }

        return response()->json([
            'response' => "I'm sorry, I don't have specific information about that topic. Here are some things I can help with:\n\n- How to get accredited as a media practitioner\n- How to register a media house\n- Required documents for applications\n- Fees and payment information\n- Office locations and contact details\n- Application tracking and status\n- Renewals and replacements\n\n**For further assistance, please contact:**\nEmail: zmcaccreditation@gmail.com\nPhone: 253509/10 or 253572/5/6\n\nOur team will be happy to help you!",
            'matched' => false
        ]);
    }
}
