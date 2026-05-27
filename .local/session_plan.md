# Objective
Implement comprehensive workflow enforcement, UI improvements, and new features for the ZMC Portal across 13 task areas. Most foundation is already in place.

# Execution Order
1. T001 (migration + payment_stage) 
2. T002-T004 + T012 (staff controller guards + audit) in parallel
3. T005-T006 (portal payment + renewal) in parallel  
4. T007-T009 (reminders, UI, profiles) in parallel
5. T010-T011 (physical intake, production)
6. T013 (seeders + docs)

# Status: COMPLETED

## Task Status
- T001: COMPLETED - Status constants, transition map, DB constraints, payment_stage, reminders/card_templates tables, user profile columns
- T002: COMPLETED - Officer approve/reject/forward/physicalIntake guards
- T003: COMPLETED - Registrar approve/fix/oversight/paymentApprove guards
- T004: COMPLETED - Accounts approve/reject/verify/cash guards
- T005: COMPLETED - Portal payment flow updates
- T006: COMPLETED - Renewal flow: lookupAccreditation API, renewal/replacement separate routes + sidebar links
- T007: COMPLETED - Registrar reminders CRUD, portal acknowledge route, reminder banners on dashboard
- T008: COMPLETED - Profile page redesign, sidebar Media Hub link, How-To page exists
- T009: COMPLETED - Profile form with DB columns (id_number, passport_number, phone2), edit/save/cancel UX, dark mode localStorage persistence, dead links already cleaned
- T010: COMPLETED - Physical intake form + process in AccreditationOfficerController
- T011: COMPLETED - ProductionController, CardTemplate model, designer blade
- T012: COMPLETED - ActivityLogger integrated throughout all controllers
- T013: COMPLETED - Seeders (staff, test applicants, applications, card templates, notices, events, system configs)
