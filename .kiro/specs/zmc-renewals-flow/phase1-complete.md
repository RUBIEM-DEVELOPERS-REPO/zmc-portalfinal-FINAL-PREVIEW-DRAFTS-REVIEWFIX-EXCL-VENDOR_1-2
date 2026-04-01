# ZMC Renewals Flow - Phase 1 Complete

## Document Information
**Date**: 2026-02-25  
**Phase**: 1 - Database & Models  
**Status**: ✅ Complete

---

## Summary

Successfully created the database foundation for the renewals workflow. Two tables created with complete relationships and 15 status constants defined.

---

## Deliverables

### Migrations (2)

1. **renewal_applications table**
   - Applicant tracking
   - Renewal type (accreditation/registration/permission)
   - Original record reference
   - Lookup status
   - Change tracking
   - Payment details
   - Status & workflow
   - Production tracking
   - Complete indexes

2. **renewal_change_requests table**
   - Change details (field, old/new values)
   - Supporting documents
   - Review status
   - Reviewer tracking

### Models (2)

1. **RenewalApplication**
   - 15 status constants
   - 5 relationships (applicant, originalApplication, paymentVerifier, lastActionBy, producer)
   - 2 hasMany relationships (changeRequests, pendingChangeRequests)
   - 3 helper methods (labels, checks)
   - 7 query scopes

2. **RenewalChangeRequest**
   - 2 relationships (renewalApplication, reviewer)
   - 3 helper methods
   - 3 query scopes

---

## Status Constants Defined

```php
RENEWAL_DRAFT
RENEWAL_TYPE_SELECTED
RENEWAL_NUMBER_ENTERED
RENEWAL_RECORD_FOUND
RENEWAL_RECORD_NOT_FOUND
RENEWAL_CONFIRMED_NO_CHANGES
RENEWAL_CONFIRMED_WITH_CHANGES
RENEWAL_PAYMENT_INITIATED
RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION
RENEWAL_PAYMENT_VERIFIED
RENEWAL_PAYMENT_REJECTED
RENEWAL_IN_PRODUCTION
RENEWAL_PRODUCED_READY_FOR_COLLECTION
RENEWAL_COLLECTED
RENEWAL_CANCELLED
```

---

## Next Steps

Phase 2: Controllers & Business Logic
- Portal renewal controller
- Accounts verification methods
- Production methods
- Workflow service

**Estimated Time**: 3 hours
