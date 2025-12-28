# Dynamic Payment Methods - Flow Diagram

## User Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     User Opens Add Funds Page                    │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│              Page Loads with "Loading..." in Dropdown            │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│         JavaScript: loadPaymentMethods() Executes                │
│              AJAX GET: /add_funds/get_payment_methods            │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                    ▼                         ▼
        ┌───────────────────┐    ┌───────────────────┐
        │  Success Response │    │   Error Response  │
        │   with Methods    │    │  or Empty List    │
        └─────────┬─────────┘    └─────────┬─────────┘
                  │                         │
                  ▼                         ▼
        ┌───────────────────┐    ┌───────────────────┐
        │  Populate Dropdown│    │  Show Error Msg   │
        │  with Methods     │    │  or "No Methods"  │
        └─────────┬─────────┘    └───────────────────┘
                  │
                  ▼
        ┌───────────────────┐
        │ Initialize Select2│
        └─────────┬─────────┘
                  │
                  ▼
        ┌───────────────────┐
        │ User Selects      │
        │ Payment Method    │
        └─────────┬─────────┘
                  │
                  ▼
        ┌───────────────────┐
        │ loadPaymentContent│
        │    is called      │
        └─────────┬─────────┘
                  │
                  ▼
        ┌───────────────────┐
        │ Show Loading Icon │
        │  in Payment Form  │
        │      Area         │
        └─────────┬─────────┘
                  │
                  ▼
        ┌───────────────────┐
        │  AJAX POST:       │
        │  get_payment_form │
        └─────────┬─────────┘
                  │
     ┌────────────┴────────────┐
     │                         │
     ▼                         ▼
┌─────────┐          ┌──────────────┐
│ Success │          │    Error     │
│ HTML    │          │   Response   │
└────┬────┘          └──────┬───────┘
     │                      │
     ▼                      ▼
┌─────────────┐    ┌────────────────┐
│ Display     │    │  Show Error    │
│ Payment Form│    │    Message     │
└─────────────┘    └────────────────┘
```

## Technical Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser Layer                            │
├─────────────────────────────────────────────────────────────────┤
│  • index.php (View)                                              │
│    - HTML structure                                              │
│    - JavaScript for AJAX calls                                   │
│    - Select2 dropdown integration                                │
│    - Loading indicators                                          │
│    - Error handling UI                                           │
└────────────────────────────────┬────────────────────────────────┘
                                 │ AJAX
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Controller Layer                            │
├─────────────────────────────────────────────────────────────────┤
│  add_funds.php Controller                                        │
│                                                                  │
│  • index()                                                       │
│    - Loads page with transaction history                        │
│    - No payment methods (loaded via AJAX)                       │
│                                                                  │
│  • get_payment_methods()                                        │
│    - Fetches active payment methods                             │
│    - Applies user-specific filters                              │
│    - Returns JSON response                                      │
│                                                                  │
│  • get_payment_form()                                           │
│    - Validates payment type                                     │
│    - Checks user access                                         │
│    - Loads payment view                                         │
│    - Returns HTML response                                      │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Model Layer                              │
├─────────────────────────────────────────────────────────────────┤
│  add_funds_model                                                 │
│    - fetch() - Query database for payment methods               │
│    - get() - Get specific records                               │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Database Layer                            │
├─────────────────────────────────────────────────────────────────┤
│  Tables:                                                         │
│    • payments (payment methods)                                  │
│      - id, type, name, params, status                           │
│    • users (user settings)                                       │
│      - settings (limit_payments)                                │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow - Payment Methods

```
Database                Controller              Browser
────────               ──────────              ───────

payments table    →    fetch active      →    AJAX Request
  status = 1           payment methods         GET /get_payment_methods
                            |
users table       →    get user          →    
  settings              settings filter        
                            |
                       apply filters      →    
                            |
                       re-index array     →    JSON Response
                            |                   {
                       return JSON         →     "status": "success",
                                                  "data": [...]
                                                }
                                           →    
                                                Populate Dropdown
                                           →    
                                                Initialize Select2
```

## Data Flow - Payment Form

```
Browser                 Controller              View Files
───────                ──────────              ──────────

User selects      →    validate           →    
payment method         payment type            

AJAX POST         →    check user         →    
/get_payment_form      access                  
                            |
                       load view file     →    paypal/index.php
                       (e.g., paypal)           or
                            |                   stripe/index.php
                       render with        →    or
                       payment params          jazzcash/index.php
                            |                   etc.
                       return HTML        →    
                                               
                                          ←    HTML Response
                                               
Display form      ←    
in tab pane            
```

## Error Handling Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                         Error Scenarios                          │
└─────────────────────────────────────────────────────────────────┘

1. Network Error
   Browser ─AJAX Fail→ JavaScript Catch ─→ Show Error Message
                       "Failed to load. Please refresh."

2. No Payment Methods
   Database (empty) → Controller (empty array) → JSON {"data": []}
                   → JavaScript → Show "No payment methods available"

3. Invalid Payment Type
   Browser ─invalid type→ Controller Validation
                       → Return Error HTML
                       → Show "Invalid payment method"

4. Access Denied
   Browser ─request→ Controller Check User Settings
                  → User Not Allowed
                  → Return Error HTML
                  → Show "No access to this method"

5. View File Missing
   Browser → Controller → load->view() Fails
          → Exception Caught
          → Return Error HTML
          → Show "Failed to load form"
```

## Caching Strategy

```
┌─────────────────────────────────────────────────────────────────┐
│                        Client-Side Cache                         │
└─────────────────────────────────────────────────────────────────┘

Payment Methods List:
  • Loaded once on page load
  • Stored in hasLoaded flag
  • Not reloaded unless page refreshed
  
Payment Forms:
  • Loaded on first selection
  • Cached in DOM (#payment-type div)
  • Check: if ($tabPane.html().trim() === '')
  • Not reloaded on subsequent selections
  
LocalStorage:
  • Stores last selected payment method
  • Restored on page load
  • Key: 'selectedPaymentType'
```

## Security Measures

```
┌─────────────────────────────────────────────────────────────────┐
│                      Security Checkpoints                        │
└─────────────────────────────────────────────────────────────────┘

Input Validation:
  ✓ Payment type checked (not empty)
  ✓ Method must exist in database
  ✓ Method must be active (status = 1)
  
Access Control:
  ✓ User session verified
  ✓ User settings checked
  ✓ limit_payments restrictions applied
  
SQL Injection Prevention:
  ✓ Using CodeIgniter query builder
  ✓ Parameterized queries
  ✓ Input sanitization by framework
  
XSS Prevention:
  ✓ JSON data properly escaped
  ✓ HTML generated server-side
  ✓ No eval() or innerHTML with user data
  
Error Handling:
  ✓ Try-catch blocks
  ✓ Generic error messages
  ✓ No sensitive data in responses
```
