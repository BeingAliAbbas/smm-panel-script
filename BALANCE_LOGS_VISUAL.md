# Balance Logs - Visual Guide

## Page Layout

### User View
```
┌─────────────────────────────────────────────────────────────────┐
│                    BALANCE LOGS PAGE                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Balance Change History                                         │
│  ─────────────────────────────────────────────────────────────  │
│                                                                 │
│  No. │ Action Type    │ Amount       │ Before  │ After   │ ... │
│  ───────────────────────────────────────────────────────────── │
│  1   │ [Addition]     │ +$100.00     │ $50.00  │ $150.00 │ ... │
│  2   │ [Deduction]    │ -$25.00      │ $150.00 │ $125.00 │ ... │
│  3   │ [Refund]       │ +$25.00      │ $125.00 │ $150.00 │ ... │
│                                                                 │
│  ◄ ◄◄ 1 2 3 ►► ►                                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Admin View
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       BALANCE LOGS PAGE (ADMIN)                             │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Balance Change History              [Search] [User Email ▼] [Go]          │
│  ─────────────────────────────────────────────────────────────────────────  │
│                                                                             │
│  No. │ User        │ Action     │ Amount    │ Before  │ After   │ Related  │
│  ──────────────────────────────────────────────────────────────────────── │
│  1   │ John Doe    │ [Addition] │ +$100.00  │ $50.00  │ $150.00 │ TXN123   │
│      │ ID: 42      │            │           │         │         │          │
│      │ john@...    │            │           │         │         │          │
│  ──────────────────────────────────────────────────────────────────────── │
│  2   │ Jane Smith  │ [Deduction]│ -$25.00   │ $200.00 │ $175.00 │ ORD456   │
│      │ ID: 43      │            │           │         │         │          │
│      │ jane@...    │            │           │         │         │          │
│                                                                             │
│  ◄ ◄◄ 1 2 3 4 5 ►► ►                                                       │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Badge Colors

Action types are color-coded for easy identification:

```
┌──────────────┬──────────┬─────────────────────────────┐
│ Action Type  │ Color    │ Description                 │
├──────────────┼──────────┼─────────────────────────────┤
│ Addition     │ 🟢 Green │ Balance increased (payment) │
│ Deduction    │ 🔴 Red   │ Balance decreased (order)   │
│ Refund       │ 🔵 Cyan  │ Balance refunded            │
│ Manual Add   │ 🔵 Blue  │ Admin added funds           │
│ Manual Deduct│ 🟠 Orange│ Admin removed funds         │
└──────────────┴──────────┴─────────────────────────────┘
```

## Amount Display

Amounts are displayed with +/- indicators:

```
Positive (Green):
  +$100.00    Payment received
  +$25.00     Order refunded

Negative (Red):
  -$50.00     Order placed
  -$10.00     Admin adjustment
```

## Example Records

### 1. Order Placement
```
Action Type:    [Deduction]
Amount:         -$45.50
Balance Before: $100.00
Balance After:  $54.50
Description:    Order placed - ID: ORD789
Related ID:     ORD789
Related Type:   order
Date & Time:    Nov 7, 2025 10:30 AM
```

### 2. Payment Received
```
Action Type:    [Addition]
Amount:         +$200.00
Balance Before: $54.50
Balance After:  $254.50
Description:    Payment received via PayPal - Transaction ID: TXN456
Related ID:     TXN456
Related Type:   transaction
Date & Time:    Nov 7, 2025 11:45 AM
```

### 3. Order Refund
```
Action Type:    [Refund]
Amount:         +$45.50
Balance Before: $254.50
Balance After:  $300.00
Description:    Refund for order - ID: ORD789
Related ID:     ORD789
Related Type:   refund
Date & Time:    Nov 7, 2025 02:15 PM
```

### 4. Manual Adjustment (Admin)
```
Action Type:    [Manual Add]
Amount:         +$50.00
Balance Before: $300.00
Balance After:  $350.00
Description:    Manual funds added by admin - Note: Compensation
Related ID:     empty
Related Type:   manual
Date & Time:    Nov 7, 2025 03:00 PM
```

## Search Interface (Admin Only)

```
┌─────────────────────────────────────────────┐
│  Search Balance Logs                        │
│  ─────────────────────────────────────────  │
│                                             │
│  Search Type: [User Email      ▼]          │
│                                             │
│  Query:      [john@example.com ]           │
│                                             │
│              [ Search ]                     │
│                                             │
└─────────────────────────────────────────────┘

Search Types:
1. User Email    - Find logs for specific user
2. Related ID    - Search by order/transaction ID
3. Action Type   - Filter by action type
```

## Mobile View

On mobile devices, the table adapts:

```
┌───────────────────────────┐
│  Balance Logs             │
├───────────────────────────┤
│                           │
│  [Addition]               │
│  +$100.00                 │
│  Before: $50.00           │
│  After: $150.00           │
│  Payment received         │
│  Nov 7, 2025 10:30 AM     │
│  ─────────────────────    │
│                           │
│  [Deduction]              │
│  -$25.00                  │
│  Before: $150.00          │
│  After: $125.00           │
│  Order placed - ORD123    │
│  Nov 7, 2025 11:15 AM     │
│  ─────────────────────    │
│                           │
│  Load More...             │
│                           │
└───────────────────────────┘
```

## Navigation Flow

```
User Login
    │
    ├─► Dashboard
    │       │
    │       ├─► Order Logs
    │       ├─► Transaction Logs
    │       └─► Balance Logs ◄── NEW!
    │               │
    │               ├─► View Personal History
    │               └─► Search Personal Logs
    │
Admin Login
    │
    ├─► Dashboard
    │       │
    │       ├─► User Manager
    │       ├─► Transaction Logs
    │       └─► Balance Logs ◄── NEW!
    │               │
    │               ├─► View All Users' History
    │               ├─► Search by Email/ID/Type
    │               ├─► Delete Logs
    │               └─► Bulk Actions
    │
```

## Integration Points

```
Order Placement Flow:
User → New Order → Calculate Cost → Check Balance
                                         │
                                         ├─► Deduct Balance
                                         │       │
                                         │       └─► LOG BALANCE CHANGE ✓
                                         │
                                         └─► Create Order

Payment Flow:
User → Add Funds → Payment Gateway → Payment Success
                                          │
                                          ├─► Update Balance
                                          │       │
                                          │       └─► LOG BALANCE CHANGE ✓
                                          │
                                          └─► Create Transaction

Refund Flow:
Admin → Order List → Cancel Order → Calculate Refund
                                          │
                                          ├─► Add to Balance
                                          │       │
                                          │       └─► LOG BALANCE CHANGE ✓
                                          │
                                          └─► Update Order Status
```

## Color Scheme

The Balance Logs page uses a dark theme matching the existing panel:

```
Background:     #06141b (Dark Blue-Black)
Card:           #042636 (Darker Blue)
Border:         #0d3242 (Teal Border)
Header:         Linear gradient (#042636 → #052d40 → #041d28)
Text:           #e9f6ff (Light Blue-White)
Positive:       #28a745 (Green)
Negative:       #dc3545 (Red)
```

## Sidebar Menu

```
┌────────────────────────┐
│  Dashboard             │
│  Services              │
│  Orders                │
│  Order Logs            │
│  Transaction Logs      │
│  Balance Logs     ◄─── NEW!
│  API                   │
│  Profile               │
└────────────────────────┘
```

## Page States

### Loading State
```
┌─────────────────────────────────┐
│  Loading balance logs...        │
│  ◐ Please wait                  │
└─────────────────────────────────┘
```

### Empty State
```
┌─────────────────────────────────┐
│  No balance logs found          │
│                                 │
│  Balance changes will appear    │
│  here once you make transactions│
└─────────────────────────────────┘
```

### Error State
```
┌─────────────────────────────────┐
│  ⚠ Error loading balance logs   │
│                                 │
│  Please try again later or      │
│  contact support                │
└─────────────────────────────────┘
```

## Responsive Breakpoints

- **Desktop** (>1024px): Full table with all columns
- **Tablet** (768-1024px): Condensed table, some columns combined
- **Mobile** (<768px): Card-based layout, stacked information

---

## Quick Access

From any page in the panel:
1. Click sidebar menu
2. Locate "Balance Logs" (below Transaction Logs)
3. Click to view
4. Use search or filters as needed

---

This visual guide helps understand the UI/UX of the Balance Logs feature before seeing the actual implementation in the browser.
