# ZMC Workflow Enforcement - User Training Guide

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**Audience**: ZMC Staff (Registrar, Accreditation Officer, Accounts)  
**Training Duration**: 30-45 minutes per role

---

## Training Overview

This guide covers the new features added to the ZMC system:
1. **Fix Request Workflow** - For Registrar and Accreditation Officer
2. **Payment Submission Tracking** - For Accounts/Payments Officer

---

## Part 1: Fix Request Workflow

### For Registrars

#### What is a Fix Request?

A Fix Request is a structured way to ask the Accreditation Officer to correct application data. Instead of editing the application yourself or sending an email, you now use the Fix Request feature.

#### When to Use Fix Requests

Use Fix Requests when you find:
- **Data Correction**: Wrong information in the application form
- **Category Change**: Incorrect category assigned by Officer
- **Document Issue**: Missing or incorrect documents

#### How to Send a Fix Request

**Step 1: Open the Application**
1. Login to your Registrar dashboard
2. Click on an application in REGISTRAR_REVIEW status
3. Review the application details

**Step 2: Identify the Issue**
- Check applicant information
- Verify category assignment
- Review uploaded documents

**Step 3: Send Fix Request**
1. Click the **"Fix Request"** button (blue button at bottom)
2. A modal window will open
3. Select the **Request Type**:
   - Data Correction
   - Category Change
   - Document Issue
4. Write a **detailed description** of what needs to be fixed
5. Click **"Send Fix Request"**

**Step 4: Track Your Request**
1. Click **"Fix Requests"** in the sidebar
2. See all your fix requests
3. Filter by status (Pending, Resolved, etc.)
4. View resolution notes from Officer

#### Example Fix Request

```
Request Type: Data Correction
Description: 
The applicant's ID number is incorrect. 
Application shows: 63-123456-A-12
Should be: 63-123456-A-21
Please verify with uploaded ID document.
```

#### Tips for Good Fix Requests

✅ **DO**:
- Be specific about what's wrong
- Reference the correct information
- Mention which document to check
- Use clear, professional language

❌ **DON'T**:
- Write vague descriptions like "fix this"
- Use ALL CAPS or excessive punctuation
- Include personal opinions
- Send multiple requests for the same issue

---

### For Accreditation Officers

#### What is a Fix Request?

A Fix Request is a notification from the Registrar that an application needs correction. You'll see these in your dashboard and must resolve them before the application can proceed.

#### How to View Fix Requests

**Method 1: Dashboard Badge**
1. Login to your Officer dashboard
2. Look at the sidebar
3. See **"Fix Requests"** with a yellow badge showing count
4. Click to view all pending requests

**Method 2: Direct Link**
1. Click **"Fix Requests"** in sidebar
2. See all fix requests in card format
3. Each card shows:
   - Application reference
   - Applicant name
   - Request type
   - Description
   - Who requested it
   - When it was requested

#### How to Resolve a Fix Request

**Step 1: Review the Request**
1. Read the description carefully
2. Note the request type
3. Click **"View Application"** to see full details

**Step 2: Make the Correction**
1. Open the application
2. Make necessary changes
3. Verify the correction is accurate

**Step 3: Resolve the Request**
1. Return to Fix Requests page
2. Click the **green checkmark button**
3. Choose action:
   - **Mark as Resolved**: You fixed the issue
   - **Cancel Request**: Request not valid
4. Add **Resolution Notes** (optional but recommended)
5. Click **"Confirm"**

**Step 4: Re-approve Application**
1. Application returns to your queue
2. Review the corrected application
3. Approve it again to send to Registrar

#### Example Resolution

```
Action: Mark as Resolved
Resolution Notes:
ID number corrected from 63-123456-A-12 to 63-123456-A-21.
Verified against uploaded ID document.
Application ready for re-approval.
```

#### Tips for Resolving Fix Requests

✅ **DO**:
- Respond promptly (within 24 hours)
- Write clear resolution notes
- Verify corrections are accurate
- Re-approve after fixing

❌ **DON'T**:
- Ignore fix requests
- Cancel without good reason
- Skip resolution notes
- Forget to re-approve

---

## Part 2: Payment Submission Tracking

### For Accounts/Payments Officers

#### What is Payment Submission Tracking?

The system now automatically tracks HOW applicants submit their payment information:
- 💳 **PayNow**: Paid through PayNow gateway
- 📄 **Proof Upload**: Uploaded proof of payment document
- 🏷️ **Waiver**: Submitted waiver request

#### Why This Matters

Different submission methods require different verification steps:
- **PayNow**: Check PayNow platform for confirmation
- **Proof Upload**: Verify uploaded document against bank records
- **Waiver**: Review waiver validity and authorization

#### How to Use the Filter

**Step 1: Access Dashboard**
1. Login to Accounts dashboard
2. See the filter section at top

**Step 2: View KPIs**
Look at the summary badges:
- Total Pending: All applications awaiting action
- PayNow: Count of PayNow submissions
- Proof Upload: Count of proof uploads
- Waiver: Count of waiver submissions
- No Submission: Applications without payment info yet

**Step 3: Filter by Method**
1. Click the **"Payment Submission Method"** dropdown
2. Select a method:
   - All Methods (default)
   - PayNow
   - Proof Upload
   - Waiver
3. Click **"Filter"** button
4. See only applications with that method

**Step 4: Reset Filter**
- Click **"Reset"** button to see all applications again

#### Understanding the Payment Method Column

In the applications table, you'll see a new column showing:

**PayNow Badge** (Blue):
```
💳 PayNow
2 hours ago
```

**Proof Upload Badge** (Cyan):
```
📄 Proof
1 day ago
```

**Waiver Badge** (Yellow):
```
🏷️ Waiver
3 hours ago
```

**No Submission Badge** (Gray):
```
❓ None
```

The timestamp shows when the applicant submitted the payment information.

#### Workflow Examples

**Example 1: Processing PayNow Submissions**
1. Select "PayNow" from filter
2. Click Filter
3. See only PayNow submissions
4. For each application:
   - Note the PayNow reference
   - Check PayNow platform
   - Verify payment received
   - Click "Mark Paid" if confirmed

**Example 2: Processing Proof Uploads**
1. Select "Proof Upload" from filter
2. Click Filter
3. See only proof uploads
4. For each application:
   - Click to view details
   - Download proof document
   - Verify against bank records
   - Approve or reject proof

**Example 3: Prioritizing Old Submissions**
1. View "All Methods"
2. Look at timestamps in Payment Method column
3. Prioritize older submissions (e.g., "3 days ago")
4. Process in order of submission time

#### Tips for Efficient Processing

✅ **DO**:
- Filter by method to batch similar tasks
- Check timestamps to prioritize old submissions
- Verify payment before approving
- Keep notes of verification steps

❌ **DON'T**:
- Approve without verification
- Ignore the submission method
- Process randomly without filtering
- Skip checking timestamps

---

## Part 3: Common Scenarios

### Scenario 1: Registrar Finds Wrong Category

**Situation**: Application has category JE but should be JF

**Steps**:
1. Registrar opens application
2. Clicks "Fix Request" button
3. Selects "Category Change"
4. Writes: "Category should be JF (freelancing) not JE (employed). Applicant indicated freelance status in form."
5. Sends fix request
6. Officer receives notification
7. Officer reviews and changes category to JF
8. Officer resolves fix request with note: "Category changed to JF as requested"
9. Officer re-approves application
10. Application returns to Registrar queue

### Scenario 2: Multiple Payment Methods Same Day

**Situation**: Accounts has 20 applications, mix of PayNow and Proof

**Steps**:
1. Officer sees KPIs: PayNow (12), Proof (8)
2. Decides to process PayNow first (faster verification)
3. Selects "PayNow" from filter
4. Clicks Filter
5. Sees only 12 PayNow applications
6. Processes all PayNow verifications
7. Clicks Reset
8. Selects "Proof Upload"
9. Processes all proof verifications

### Scenario 3: Urgent Application Needs Fast Fix

**Situation**: VIP application has wrong phone number

**Steps**:
1. Registrar sends fix request immediately
2. Marks as "Data Correction"
3. Writes: "URGENT: VIP application. Phone number incorrect. Should be +263 77 123 4567"
4. Officer sees fix request
5. Officer corrects phone number within 1 hour
6. Officer resolves with note: "Phone corrected. Re-approved."
7. Application proceeds same day

---

## Part 4: Troubleshooting

### Fix Request Issues

**Problem**: Can't see "Fix Request" button

**Solution**:
- Check application status (must be REGISTRAR_REVIEW)
- Verify you're logged in as Registrar
- Refresh the page
- Contact IT if still not visible

**Problem**: Fix request not appearing in Officer's queue

**Solution**:
- Wait a few seconds and refresh
- Check if application status changed
- Verify fix request was created (check your Fix Requests page)
- Contact IT if issue persists

**Problem**: Can't resolve fix request

**Solution**:
- Verify you're logged in as Accreditation Officer
- Check if fix request is still pending
- Try refreshing the page
- Contact IT if button not working

### Payment Filter Issues

**Problem**: Filter not working

**Solution**:
- Click Reset button first
- Select method again
- Click Filter button
- Clear browser cache if needed
- Contact IT if still not working

**Problem**: Payment method shows "None"

**Solution**:
- This is normal for old applications
- Applicant hasn't submitted payment yet
- Or application submitted before system update
- No action needed

**Problem**: Wrong count in KPI badges

**Solution**:
- Refresh the page
- Counts update when page loads
- If still wrong, contact IT

---

## Part 5: Best Practices

### For All Users

1. **Check Daily**
   - Login at start of day
   - Check your queue/dashboard
   - Prioritize urgent items

2. **Communicate Clearly**
   - Write clear descriptions
   - Use professional language
   - Include all relevant details

3. **Track Your Work**
   - Use the list pages to track progress
   - Check resolved items
   - Follow up on pending items

4. **Report Issues**
   - Note any system errors
   - Report to IT immediately
   - Document what you were doing

### For Registrars

1. **Before Sending Fix Request**
   - Double-check the issue
   - Verify it's not your mistake
   - Check if you can reassign category yourself

2. **Writing Fix Requests**
   - Be specific and detailed
   - Reference documents
   - Suggest the correction

3. **After Sending**
   - Track in Fix Requests page
   - Follow up if no response in 24 hours
   - Check resolution notes

### For Officers

1. **Responding to Fix Requests**
   - Respond within 24 hours
   - Make corrections carefully
   - Write clear resolution notes

2. **After Resolving**
   - Re-approve the application
   - Verify it returns to Registrar
   - Keep track of common issues

### For Accounts

1. **Using Filters**
   - Start day by checking KPIs
   - Filter by method for efficiency
   - Process in batches

2. **Verifying Payments**
   - Always verify before approving
   - Check timestamps for priority
   - Document verification steps

---

## Part 6: Quick Reference

### Fix Request Quick Guide

| Action | Steps |
|--------|-------|
| Send Fix Request | Application → Fix Request button → Fill form → Send |
| View Fix Requests | Sidebar → Fix Requests |
| Resolve Fix Request | Fix Requests → Checkmark → Fill form → Confirm |
| Track Status | Fix Requests page → Filter by status |

### Payment Filter Quick Guide

| Action | Steps |
|--------|-------|
| View KPIs | Dashboard → See badges at top |
| Filter by Method | Dashboard → Dropdown → Select → Filter |
| Reset Filter | Dashboard → Reset button |
| Check Submission Time | Table → Payment Method column → See timestamp |

### Status Reference

| Status | Meaning | Next Step |
|--------|---------|-----------|
| Pending | Awaiting action | Resolve or process |
| In Progress | Being worked on | Complete action |
| Resolved | Fixed and closed | No action needed |
| Cancelled | Request cancelled | No action needed |

---

## Part 7: Training Exercises

### Exercise 1: Send a Fix Request (Registrar)

**Scenario**: Application APP-12345 has wrong email address

**Task**:
1. Open application APP-12345
2. Send fix request for data correction
3. Description: "Email should be john@example.com not john@exmaple.com"
4. Check Fix Requests page to confirm

### Exercise 2: Resolve a Fix Request (Officer)

**Scenario**: You have a pending fix request for wrong category

**Task**:
1. Go to Fix Requests page
2. Find the request
3. View the application
4. Change the category
5. Resolve the fix request
6. Add resolution notes
7. Re-approve the application

### Exercise 3: Filter by Payment Method (Accounts)

**Scenario**: You have 15 applications to process

**Task**:
1. Check KPIs to see breakdown
2. Filter by PayNow
3. Process PayNow applications
4. Reset filter
5. Filter by Proof Upload
6. Process proof applications

---

## Training Completion Checklist

### Registrar
- [ ] Understand what fix requests are
- [ ] Know when to use fix requests
- [ ] Can send a fix request
- [ ] Can track fix request status
- [ ] Can view resolution notes

### Accreditation Officer
- [ ] Understand fix request notifications
- [ ] Can view pending fix requests
- [ ] Can resolve fix requests
- [ ] Can write resolution notes
- [ ] Can re-approve after fixing

### Accounts Officer
- [ ] Understand payment submission tracking
- [ ] Can view KPIs
- [ ] Can filter by payment method
- [ ] Can identify submission method from badges
- [ ] Can prioritize by timestamp

---

## Support and Help

### Getting Help

**During Training**:
- Ask trainer questions
- Practice with test data
- Request additional examples

**After Training**:
- Contact IT Support Desk
- Email: support@zmc.co.zw
- Phone: +263 242 703351
- Reference this guide

### Additional Resources

- System User Manual: Available on portal
- Video Tutorials: Coming soon
- FAQ Document: Available on portal
- IT Support: Available 8AM-5PM weekdays

---

**Training Guide Prepared By**: Kiro AI  
**Date**: 2026-02-25  
**Version**: 1.0  
**Status**: Ready for Training Sessions ✅
