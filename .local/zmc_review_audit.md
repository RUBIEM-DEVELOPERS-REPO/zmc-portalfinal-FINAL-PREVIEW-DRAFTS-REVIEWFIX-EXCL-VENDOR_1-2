# ZMC Review Documents - Compliance Audit

## REVIEW 1 (Registration and Accreditation System Review 1)

### Home Page & Authentication
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 1 | Green colour blends with diminished opacity, ZMC building more visible | DONE | Opacity reduced Feb 26, background image visible |
| 2 | System timeout to avoid idleness | PARTIAL | Laravel session lifetime = 120min; no front-end idle warning/countdown |
| 3 | Auto-select role on login (no role selection page) | NOT DONE | Staff still see role selection page at /staff/select-role |
| 4 | Add all countries in Phone Country Code, change label to "Country Code" | NOT DONE | Not verified in registration form |

### Media Practitioner Portal
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 5 | Separate renewal and replacement links | DONE | Separate renewal_type radio: renewal vs replacement |
| 6 | Notices & Events from website in aesthetic design | PARTIAL | notices.blade.php exists but design may not match website |
| 7 | Downloads - appear as from website | PARTIAL | downloads/index.blade.php exists |
| 8 | Requirements - appear as from website | PARTIAL | requirements.blade.php exists |
| 9 | How it works - Remove Expected Timelines section | NOT VERIFIED | howto.blade.php exists |
| 10 | Remove "Processing days" | NOT VERIFIED | |
| 11 | Remove "Zimbabwe Media Commission Act (2020)", leave statutory instrument | NOT VERIFIED | |
| 12 | Add all countries in phone code | NOT DONE | |
| 13 | Add drafts tab for easy tracking | DONE | Draft saving implemented |
| 14 | Auto-pick date/time for declaration submission | NOT VERIFIED | |
| 15 | Past work sample upload tab | PARTIAL | Document upload exists |
| 16 | Info boxes with web links for online work (optional) | NOT VERIFIED | |
| 17 | Submission part taking time/not submitting - fix | NOT VERIFIED | |

### Renewal/Replacement
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 18 | Separate renew and replace links in portal | DONE | Two radio options |
| 19 | Add accreditation number + national ID for locals | NOT VERIFIED | |
| 20 | Add accreditation number + passport for foreigners | NOT VERIFIED | |
| 21 | Payment done automatically via PayNow as they submit | PARTIAL | PayNow integration exists but not automatic on submit |

### Accreditation Officer Dashboard
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 22 | Remove rejected link, leave "returned for correction" | PARTIAL | Sidebar shows "Rejected / Returned" - should be just "Returned" |
| 23 | Indicate total applications and processed for year on records | NOT DONE | Records lack analytics |
| 24 | Change "New Application" to "Recent Application" | NOT DONE | Still says "New Applications" |
| 25 | Pending review = all unattended applications | DONE | Pending review filter exists |
| 26 | Approved = all pushed for production | DONE | Approved filter exists |
| 27 | Incoming applications must NOT be exported | NOT VERIFIED | Export route exists but may not be linked |
| 28 | Filter by name, type, email, ref, date, month, year | PARTIAL | Filters exist but may not have all fields |
| 29 | Fields: ref, name+email, type, submission date+time, action | PARTIAL | |
| 30 | Remove status column (repetition) | NOT DONE | Status still shown |
| 31 | Remove category column (not yet assigned) | NOT DONE | |
| 32 | Message icon = seek guidance from Registrar | PARTIAL | Message functionality exists |
| 33 | Records section: data analytics with graphs | NOT DONE | No charts in records views |
| 34 | Records section: export in various formats | NOT DONE | No export buttons in records views |
| 35 | Physical Intake form | DONE | View exists but NOT linked in sidebar |
| 36 | Production Queue on officer dashboard | EXISTS | Should only be in Production dashboard |
| 37 | Switch to Production and Designer buttons | PARTIAL | Production and designer views exist |

### Record Fields Required
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 38 | Accredited Media Practitioners: all listed fields (media number, name, org, category, valid from/to, month, year, ID, photo, marital status, sex, DOB, birthplace, nationality, address, town, province, phones, medium, designation, email) | PARTIAL | Basic fields exist but many missing from view |
| 39 | Fields preserved per year (no wiping) | NOT VERIFIED | |
| 40 | Registered Media Houses: all listed fields (reg number, org, directors, shareholding, address, phones, email, website, category, status, date, year, publications/services with sub-fields) | PARTIAL | Basic fields exist but many sub-fields missing |
| 41 | Database data editable upon registrar approval | NOT DONE | |

---

## REVIEW 2 (Registration and Accreditation System Review 2)

### Media House Batch Processing
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 42 | Media House batch processing for media practitioners | NOT DONE | Only individual journalist linking exists |
| 43 | Media Services view list of practitioners in their domain | PARTIAL | Staff index exists for media houses |
| 44 | Notify Commission on departing practitioners | NOT DONE | |
| 45 | Batch payment with POP/PayNow/Cash | NOT DONE | |
| 46 | Individual notifications after batch payment | NOT DONE | |
| 47 | Officer and Accountant see batch applications | NOT DONE | |

### New Profiles Required
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 48 | PR (Public Relations) profile | NOT DONE | Role not seeded |
| 49 | Public Information Compliance profile | NOT DONE | Role not seeded |
| 50 | Research, Training and Standards Development profile | NOT DONE | Role not seeded |
| 51 | Chief Accountant profile (supervises accountant) | NOT DONE | Role not seeded |

### Accountant Profile Changes
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 52 | Change "Total Queue" to "Total Applications" | NOT DONE | |
| 53 | Add "Paid via Pay Now" link (auto-processed) | PARTIAL | PayNow transactions view exists |
| 54 | Auto-receipt from PayNow after each transaction | NOT DONE | |
| 55 | Add "Paid via Uploads" link (POP, receipt, waiver, exemptions) | PARTIAL | Proof/waiver views exist |
| 56 | Pending Action = uploaded proofs awaiting confirmation | DONE | Dashboard shows pending proofs |
| 57 | Approved items go to production AND appear on Officer dashboard | PARTIAL | Goes to production; officer visibility unclear |
| 58 | "Paid Confirmed" changed to "Approved (Paid)" | NOT DONE | Still says "Paid Confirmed" |
| 59 | Remove "Awaiting Verification" link (repetition) | NOT VERIFIED | |
| 60 | Remove Audit Reports from accounts | NOT DONE | Still in sidebar |
| 61 | Remove User Action Logs (move to IT) | NOT DONE | Still in accounts sidebar |
| 62 | Download option for each category | PARTIAL | Some export routes exist |
| 63 | Analytics dashboards with graphs/trends per category | PARTIAL | Financial reports exist |
| 64 | Digital receipt functionality (send to applicants) | PARTIAL | PDF template exists, send mechanism unclear |

### Registrar Profile Changes
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 65 | Registrar = supervisory role (officer approves for printing) | PARTIAL | Workflow supports this but UI not fully aligned |
| 66 | Registrar reviews approved apps with checkboxes | NOT DONE | |
| 67 | Officer sees which apps registrar has/hasn't reviewed | NOT DONE | |
| 68 | "Awaiting Registrar" changed to "All Applications" | NOT DONE | |
| 69 | "Returned for Correction" = apps returned by officer + reason | PARTIAL | Returns exist but labeling unclear |
| 70 | "Forwarded to Registrar" for complex applications | DONE | Forward functionality exists |
| 71 | Remove "Category Mismatch" | NOT VERIFIED | |
| 72 | Remove "Fix Requests" from sidebar | NOT DONE | Fix Requests still in registrar sidebar |
| 73 | "Print Jobs Today" changed to "Cards Generated" | NOT DONE | |
| 74 | Activity Feed = activity between Officer and Accountant | PARTIAL | Activity feed exists |
| 75 | Reassign option goes to Accreditation Officer only | NOT VERIFIED | |
| 76 | Keep only: View Application + Send Message to Officer | NOT DONE | More options still present |
| 77 | Incoming queue: view all apps + status, filter like Officer | PARTIAL | Incoming queue exists |
| 78 | "New Submissions" changed to "All Applications" + filters | NOT DONE | |
| 79 | Approved tab: categorize renewals/new/replacements | NOT DONE | |
| 80 | Remove "Rejected" | NOT DONE | Still in sidebar |
| 81 | Operational Reports = Officer activities | PARTIAL | Reports exist |
| 82 | Remove "Audit Trail Search" | NOT DONE | Still in sidebar |
| 83 | Downloads: registration/accreditation content only | NOT VERIFIED | |
| 84 | All profiles see notices/events/news (no edit rights) | PARTIAL | Views exist but not all profiles |
| 85 | Registrar sees payment statuses (no payment oversight) | PARTIAL | Payment oversight view exists |
| 86 | Records tab from Officer also visible here | PARTIAL | |
| 87 | Download and print reports | PARTIAL | Some report views exist |
| 88 | Analytics: accreditation/registration only (downloadable) | NOT DONE | |
| 89 | Remove "Users and Access" tabs | NOT VERIFIED | |
| 90 | News/Press Statements clickable to read more | NOT VERIFIED | |

### Auditor Dashboard
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 91 | Change "rejected" to "returned" for all tabs | NOT DONE | |
| 92 | Apply analytics design from auditor to other dashboards | NOT DONE | |
| 93 | Remove "irregular" tab | NOT VERIFIED | |
| 94 | Add security oversight to IT Dashboard and Super User | PARTIAL | Security view exists in IT |

### Director Media Development and Governance Dashboard
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 95 | Change to "Director Media Development and Governance Dashboard" | NOT DONE | Still "CEO Strategic Intelligence" |
| 96 | "Registrar Approval" to "Registrar Reviews" | NOT DONE | |
| 97 | Change "rejected" to "returned for correction" | NOT DONE | |
| 98 | "CEO Strategic Intelligence" to "Director MDG's Strategic Oversight" | NOT DONE | |
| 99 | "Executive Overview" to "Overview" | NOT DONE | |
| 100 | Add Financial Overview and Compliance & Risk to Auditor | PARTIAL | Director has these; auditor may not |
| 101 | Remove Staff Performance (responsibility of Super User) | NOT DONE | Still present |

### IT Administrator Changes
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 102 | Drafts visible to IT, Officer, Registrar, Accountant | PARTIAL | Some visibility exists |
| 103 | Draft disclaimer: 2-week expiry, auto-delete | NOT DONE | |
| 104 | Remove "Recover" option, place "Review" | NOT VERIFIED | |
| 105 | IT cannot download files (view only) | NOT DONE | |
| 106 | Remove accreditation/registration trends from IT | NOT DONE | Still in IT dashboard |
| 107 | Push trends to Officer, Registrar, Super Admin, Director, Auditor, Chief Accountant | PARTIAL | Director has trends |
| 108 | Remove payment tracking from IT | NOT DONE | |
| 109 | Push payment tracking to Finance, Audit, Super User | PARTIAL | Accounts has it |
| 110 | Remove Accreditation Summary from IT | NOT DONE | |
| 111 | IT should not monitor revenue | NOT DONE | |
| 112 | Direct Permissions list all actions per role | PARTIAL | Spatie permissions exist |
| 113 | Audit trail for role assignments (date, time) | NOT DONE | |
| 114 | IT assists with temporary credentials for role changes | NOT VERIFIED | |
| 115 | Remove Notices/Events upload from IT, push to PR | NOT DONE | Still in IT |
| 116 | Remove News upload from IT, push to PR | NOT DONE | Still in IT |
| 117 | Remove Complaints/Appeals from IT, push to Public Info Compliance | NOT DONE | |
| 118 | Remove Applications from IT | NOT VERIFIED | |
| 119 | Add more visualizations (doughnut charts etc.) | NOT DONE | |
| 120 | Notices/Events/News on every profile (view only, no download) | PARTIAL | |
| 121 | PR gets download option for notices/events/news | NOT DONE | PR role doesn't exist |

### Super Admin
| # | Requirement | Status | Notes |
|---|-------------|--------|-------|
| 122 | Super User has all rights to perform any action | PARTIAL | super_admin role exists with broad access |
| 123 | All functions under Super Admin supervision with IT help | PARTIAL | |

---

## SUMMARY STATISTICS

### By Status:
- **DONE**: ~15 items
- **PARTIAL**: ~30 items  
- **NOT DONE**: ~55 items
- **NOT VERIFIED**: ~23 items

### Critical Missing Features (High Priority):
1. New roles: PR, Public Information Compliance, Research/Training, Chief Accountant
2. Media House batch processing
3. Director dashboard rename (CEO → Director MDG)
4. "Rejected" → "Returned for correction" system-wide
5. Remove audit/user logs from Accounts sidebar
6. Remove content management from IT (push to PR)
7. Remove accreditation trends/payment tracking from IT
8. Staff Performance removal from Director
9. Auto-role selection on login
10. Draft 2-week expiry with auto-deletion
11. Records section: analytics, export buttons, complete field sets
12. Registrar supervisory checkbox review system
