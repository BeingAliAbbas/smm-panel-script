# WhatsApp Verification User Flow Diagram

This document visualizes the complete user flow for WhatsApp verification after Google sign-in.

## Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      USER STARTS HERE                            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Login Page      │
                    │  (auth/login)    │
                    └──────────────────┘
                              │
                 ┌────────────┼────────────┐
                 │                         │
                 ▼                         ▼
      ┌──────────────────┐      ┌──────────────────┐
      │  Manual Login    │      │  Google Login    │
      │  (email/pass)    │      │  OAuth Flow      │
      └──────────────────┘      └──────────────────┘
                 │                         │
                 │                         │
                 ▼                         ▼
      ┌──────────────────┐      ┌──────────────────────────┐
      │ Check Credentials│      │  Google Authentication   │
      └──────────────────┘      │  Success                 │
                 │               └──────────────────────────┘
                 │                         │
                 │                         ▼
                 │               ┌──────────────────────────┐
                 │               │ Check: Existing User?    │
                 │               └──────────────────────────┘
                 │                    │              │
                 │                    │ NO           │ YES
                 │                    ▼              ▼
                 │          ┌──────────────┐  ┌─────────────┐
                 │          │ Create User  │  │ Load User   │
                 │          │ signup_type  │  │ Data        │
                 │          │ = 'google'   │  │             │
                 │          └──────────────┘  └─────────────┘
                 │                    │              │
                 │                    └──────┬───────┘
                 │                           │
                 │                           ▼
                 │               ┌──────────────────────────┐
                 │               │ Check: whatsapp_verified │
                 │               │ = 1 ?                    │
                 │               └──────────────────────────┘
                 │                    │              │
                 │                    │ NO           │ YES
                 │                    ▼              │
                 │          ┌──────────────────┐    │
                 │          │ Redirect to      │    │
                 │          │ WhatsApp Verify  │    │
                 │          │ Page             │    │
                 │          └──────────────────┘    │
                 │                    │              │
                 │                    ▼              │
                 │          ┌──────────────────┐    │
                 │          │ Enter Country    │    │
                 │          │ Code & Phone     │    │
                 │          └──────────────────┘    │
                 │                    │              │
                 │                    ▼              │
                 │          ┌──────────────────┐    │
                 │          │ Send OTP via     │    │
                 │          │ WhatsApp         │    │
                 │          └──────────────────┘    │
                 │                    │              │
                 │                    ▼              │
                 │          ┌──────────────────┐    │
                 │          │ Enter 6-Digit    │    │
                 │          │ OTP Code         │    │
                 │          └──────────────────┘    │
                 │                    │              │
                 │                    ▼              │
                 │          ┌──────────────────┐    │
                 │          │ Verify OTP       │    │
                 │          └──────────────────┘    │
                 │                    │              │
                 │              ┌─────┴─────┐        │
                 │              │           │        │
                 │          VALID      INVALID       │
                 │              │           │        │
                 │              ▼           ▼        │
                 │     ┌──────────┐  ┌──────────┐   │
                 │     │ Set      │  │ Show     │   │
                 │     │ verified │  │ Error    │   │
                 │     │ = 1      │  │ Message  │   │
                 │     └──────────┘  └──────────┘   │
                 │              │           │        │
                 │              │           ▼        │
                 │              │     ┌──────────┐   │
                 │              │     │ Retry?   │   │
                 │              │     │ (max 5)  │   │
                 │              │     └──────────┘   │
                 │              │           │        │
                 │              │           └───┐    │
                 │              │               │    │
                 └──────────────┴───────────────┴────┘
                                │
                                ▼
                    ┌──────────────────────┐
                    │  User Verified!      │
                    │  Access Granted      │
                    └──────────────────────┘
                                │
                                ▼
                    ┌──────────────────────┐
                    │  Dashboard           │
                    │  (statistics)        │
                    └──────────────────────┘
```

## Key Features

### ✅ Mandatory WhatsApp Verification for Google Users
- Cannot access dashboard without verification
- Protected by hook on every page load

### ✅ International Phone Support
- 80+ countries supported
- Proper country code validation
- International format storage

### ✅ Secure OTP System
- 6-digit random code
- 10-minute expiry
- 5 attempt limit
- 60-second resend cooldown

### ✅ User Type Tracking
- Manual users: Direct access
- Google users: Verification required
- Clear distinction in database

---

See SETUP_INSTRUCTIONS.md for complete setup guide.
See WHATSAPP_VERIFICATION_README.md for detailed documentation.
