# Authentication Flow Diagrams

## 1. Google Sign-In Flow (NEW - With WhatsApp Verification)

```
┌─────────────────────────────────────────────────────────────────┐
│                    Google Sign-In Process                       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  User clicks      │
                    │  "Sign in with   │
                    │   Google"        │
                    └──────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Redirect to     │
                    │  Google OAuth    │
                    └──────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  User grants     │
                    │  permissions     │
                    └──────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Google callback │
                    │  with user info  │
                    └──────────────────┘
                              │
                              ▼
                  ┌──────────────────────┐
                  │  Check if user       │
                  │  exists in database  │
                  └──────────────────────┘
                    │                   │
          ┌─────────┴─────────┐        │
          │                   │        │
    User Exists         User New      │
          │                   │        │
          ▼                   ▼        │
  ┌──────────────┐    ┌──────────────┐│
  │ Update user  │    │ Create user  ││
  │ google_id    │    │ with         ││
  │ if missing   │    │ google_id    ││
  └──────────────┘    └──────────────┘│
          │                   │        │
          └─────────┬─────────┘        │
                    │                  │
                    ▼                  │
          ┌──────────────────┐         │
          │  Set session     │         │
          │  (uid)           │         │
          └──────────────────┘         │
                    │                  │
                    ▼                  │
          ┌──────────────────────────┐ │
          │  Check if WhatsApp       │ │
          │  is verified?            │ │
          └──────────────────────────┘ │
                    │                  │
          ┌─────────┴─────────┐        │
          │                   │        │
    Not Verified         Verified     │
          │                   │        │
          ▼                   ▼        │
┌──────────────────────┐  ┌──────────────┐
│ ╔══════════════════╗ │  │ Redirect to  │
│ ║ Redirect to      ║ │  │ Dashboard    │
│ ║ WhatsApp Setup   ║ │  │ (statistics) │
│ ╚══════════════════╝ │  └──────────────┘
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  WhatsApp Setup Page │                 
│  - Country selector  │                 
│  - Phone input       │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  User submits phone  │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────────┐             
│  ╔════════════════════╗  │             
│  ║ Rate limit check   ║  │             
│  ║ (3 per 15 min)     ║  │             
│  ╚════════════════════╝  │             
└──────────────────────────┘             
          │                              
          ▼                              
┌──────────────────────┐                 
│  Generate 6-digit    │                 
│  OTP (5 min expiry)  │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  Store OTP in DB     │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  Send OTP via        │                 
│  WhatsApp API        │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  Redirect to OTP     │                 
│  Verification Page   │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────┐                 
│  User enters OTP     │                 
└──────────────────────┘                 
          │                              
          ▼                              
┌──────────────────────────┐             
│  ╔════════════════════╗  │             
│  ║ Verify OTP         ║  │             
│  ║ - Check expiry     ║  │             
│  ║ - Check attempts   ║  │             
│  ║ - Match code       ║  │             
│  ╚════════════════════╝  │             
└──────────────────────────┘             
          │                              
    ┌─────┴─────┐                        
    │           │                        
  Valid     Invalid                      
    │           │                        
    ▼           ▼                        
┌────────┐  ┌─────────────────┐          
│ Mark   │  │ Show error      │          
│ as     │  │ Increment       │          
│verified│  │ attempts        │          
└────────┘  └─────────────────┘          
    │                                    
    ▼                                    
┌──────────────────────┐                 
│  Update user:        │                 
│  - whatsapp_verified │                 
│  - whatsapp_number   │                 
└──────────────────────┘                 
    │                                    
    ▼                                    
┌──────────────────────┐                 
│  ╔════════════════╗  │                 
│  ║ Success!       ║  │                 
│  ║ Redirect to    ║  │                 
│  ║ Dashboard      ║  │                 
│  ╚════════════════╝  │                 
└──────────────────────┘                 
```

## 2. Manual Sign-Up Flow (UNCHANGED)

```
┌─────────────────────────────────────────────────────────────────┐
│                  Manual Sign-Up Process                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  User clicks     │
                    │  "Sign Up"       │
                    └──────────────────┘
                              │
                              ▼
                    ┌──────────────────────┐
                    │  Fill signup form:   │
                    │  - First/Last name   │
                    │  - Email             │
                    │  - WhatsApp number   │
                    │  - Password          │
                    │  - Timezone          │
                    └──────────────────────┘
                              │
                              ▼
                    ┌──────────────────────┐
                    │  Validate inputs     │
                    │  (server-side)       │
                    └──────────────────────┘
                              │
                              ▼
                    ┌──────────────────────┐
                    │  Create user with:   │
                    │  - signup_type:      │
                    │    'manual'          │
                    │  - whatsapp_verified:│
                    │    1 (auto)          │
                    └──────────────────────┘
                              │
                              ▼
              ┌───────────────────────────┐
              │  Verification required?   │
              └───────────────────────────┘
                    │              │
              ┌─────┴──────┐       │
              │            │       │
          Yes (Email)   No         │
              │            │       │
              ▼            ▼       │
    ┌─────────────┐  ┌────────────┐
    │ Send email  │  │ Set session│
    │ verification│  │ (uid)      │
    └─────────────┘  └────────────┘
              │            │       │
              ▼            ▼       │
    ┌─────────────┐  ┌────────────┐
    │ Show message│  │ Redirect to│
    │ check email │  │ Dashboard  │
    └─────────────┘  └────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  ✓ Complete!     │
                    │  No additional   │
                    │  verification    │
                    └──────────────────┘
```

## 3. Security Guard (Verification Enforcement)

```
┌─────────────────────────────────────────────────────────────────┐
│              Every Request to Protected Routes                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Hook triggered  │
                    │  (post_          │
                    │  controller_     │
                    │  constructor)    │
                    └──────────────────┘
                              │
                              ▼
                    ┌──────────────────────┐
                    │  Check current route │
                    └──────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
          Allowed Route      Protected Route
          (auth, assets)     (dashboard, etc.)
                    │                   │
                    ▼                   ▼
          ┌──────────────┐    ┌──────────────────┐
          │ Allow access │    │ Check if user    │
          │ (skip check) │    │ is logged in?    │
          └──────────────┘    └──────────────────┘
                                        │
                              ┌─────────┴─────────┐
                              │                   │
                        Not Logged In       Logged In
                              │                   │
                              ▼                   ▼
                    ┌──────────────┐    ┌──────────────────┐
                    │ Allow        │    │ Get user from DB │
                    │ (handled by  │    │ - signup_type    │
                    │ other auth)  │    │ - whatsapp_      │
                    └──────────────┘    │   verified       │
                                        └──────────────────┘
                                                  │
                                        ┌─────────┴─────────┐
                                        │                   │
                                  Manual User         Google User
                                        │                   │
                                        ▼                   ▼
                              ┌──────────────┐    ┌──────────────────┐
                              │ Allow access │    │ Check WhatsApp   │
                              │ (no extra    │    │ verified?        │
                              │  verification│    └──────────────────┘
                              │  needed)     │              │
                              └──────────────┘    ┌─────────┴─────────┐
                                                  │                   │
                                            Verified          Not Verified
                                                  │                   │
                                                  ▼                   ▼
                                        ┌──────────────┐    ┌──────────────────┐
                                        │ Allow access │    │ ╔═══════════════╗│
                                        │ Continue to  │    │ ║ BLOCK ACCESS  ║│
                                        │ dashboard    │    │ ║ Redirect to   ║│
                                        └──────────────┘    │ ║ WhatsApp      ║│
                                                            │ ║ verification  ║│
                                                            │ ╚═══════════════╝│
                                                            └──────────────────┘
```

## 4. Rate Limiting Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  OTP Request Rate Limiting                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  User requests   │
                    │  OTP             │
                    └──────────────────┘
                              │
                              ▼
                    ┌────────────────────────┐
                    │  Check rate_limit      │
                    │  table for user        │
                    └────────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
          No Record Found      Record Found
                    │                   │
                    ▼                   ▼
          ┌──────────────┐    ┌──────────────────────┐
          │ Create new   │    │ Check time window    │
          │ rate limit   │    │ (15 minutes)         │
          │ record       │    └──────────────────────┘
          └──────────────┘              │
                    │         ┌─────────┴─────────┐
                    │         │                   │
                    │   Within Window      Outside Window
                    │         │                   │
                    │         ▼                   ▼
                    │   ┌──────────────┐    ┌──────────────┐
                    │   │ Check count  │    │ Reset counter│
                    │   └──────────────┘    │ (new window) │
                    │         │             └──────────────┘
                    │   ┌─────┴─────┐             │
                    │   │           │             │
                    │ < 3 requests  ≥ 3 requests  │
                    │   │           │             │
                    │   ▼           ▼             │
                    │ ┌─────┐  ┌──────────┐       │
                    │ │Allow│  │ ╔══════╗ │       │
                    └─┤Send │  │ ║BLOCK ║ │       │
                      │OTP  │  │ ║Show  ║ │       │
                      └─────┘  │ ║Error ║ │       │
                         │     │ ╚══════╝ │       │
                         │     └──────────┘       │
                         └────────┬───────────────┘
                                  │
                                  ▼
                        ┌──────────────────┐
                        │ Increment count  │
                        │ Update timestamp │
                        └──────────────────┘
```

## Key Points

### ✅ Security Features
1. **Rate Limiting**: 3 OTP requests per 15 minutes per user
2. **OTP Expiry**: Codes expire after 5 minutes
3. **Attempt Limiting**: Maximum 5 verification attempts per OTP
4. **Hook-Based Guard**: Automatic enforcement on every request
5. **Session Validation**: Server-side verification of all steps

### ✅ User Experience
1. **Clear Flow**: Step-by-step process with feedback
2. **International Support**: 200+ countries supported
3. **Resend Option**: Users can request new OTP if needed
4. **Error Messages**: Clear, helpful error messages
5. **Change Number**: Option to go back and change number

### ✅ Backward Compatibility
1. **Manual Users**: No changes, work exactly as before
2. **Existing Google Users**: Prompted to verify on next login
3. **No Breaking Changes**: All existing functionality preserved

---

**Note**: The diagrams use ASCII art for compatibility. For production documentation, 
consider using proper flowchart tools like Mermaid, Draw.io, or Lucidchart.
