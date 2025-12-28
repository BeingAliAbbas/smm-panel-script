<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Email from {{website_name}}</title>
    <style type="text/css" rel="stylesheet" media="all">
    /* Base */
    *:not(br):not(tr):not(html) {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      box-sizing: border-box;
    }
    
    body {
      width: 100% !important;
      height: 100%;
      margin: 0;
      line-height: 1.6;
      background-color: #F7F9FC;
      color: #51545E;
      -webkit-text-size-adjust: none;
    }
    
    a {
      color: #3869D4;
      text-decoration: none;
    }
    
    .email-wrapper {
      width: 100%;
      margin: 0;
      padding: 0;
      background-color: #F7F9FC;
    }
    
    .email-content {
      width: 100%;
      margin: 0;
      padding: 0;
    }
    
    .email-masthead {
      padding: 25px 0;
      text-align: center;
      background-color: #fff;
      border-bottom: 1px solid #EDEFF2;
    }
    
    .email-masthead_logo {
      max-width: 200px;
      height: auto;
    }
    
    .email-masthead_name {
      font-size: 18px;
      font-weight: bold;
      color: #3869D4;
      text-decoration: none;
    }
    
    .email-body {
      width: 100%;
      margin: 0;
      padding: 0;
      background-color: #FFFFFF;
      border-top: 1px solid #EDEFF2;
      border-bottom: 1px solid #EDEFF2;
    }
    
    .email-body_inner {
      width: 600px;
      margin: 0 auto;
      padding: 0;
      background-color: #FFFFFF;
    }
    
    .content-cell {
      padding: 40px 35px;
    }
    
    .email-footer {
      width: 600px;
      margin: 0 auto;
      padding: 25px;
      text-align: center;
    }
    
    .email-footer p {
      color: #A8AAAF;
      font-size: 13px;
      line-height: 1.5;
      margin: 0;
    }
    
    .button {
      background-color: #3869D4;
      border-radius: 4px;
      color: #fff !important;
      display: inline-block;
      font-size: 15px;
      font-weight: bold;
      line-height: 45px;
      text-align: center;
      text-decoration: none;
      padding: 0 30px;
      -webkit-text-size-adjust: none;
      margin: 25px 0;
    }
    
    .button--green {
      background-color: #22BC66;
    }
    
    .button--red {
      background-color: #FF6136;
    }
    
    .info-box {
      background-color: #F7F9FC;
      border: 1px solid #EDEFF2;
      border-radius: 4px;
      padding: 20px;
      margin: 25px 0;
    }
    
    .info-box table {
      width: 100%;
    }
    
    .info-box td {
      padding: 8px 0;
      border-bottom: 1px solid #EDEFF2;
    }
    
    .info-box td:first-child {
      font-weight: 600;
      color: #333;
      width: 40%;
    }
    
    .info-box tr:last-child td {
      border-bottom: none;
    }
    
    h1 {
      margin-top: 0;
      color: #2F3133;
      font-size: 24px;
      font-weight: 600;
      line-height: 1.3;
    }
    
    h2 {
      margin-top: 0;
      color: #2F3133;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.3;
    }
    
    p {
      margin: 0 0 15px;
      color: #51545E;
      font-size: 16px;
      line-height: 1.6;
    }
    
    p.sub {
      font-size: 13px;
      color: #A8AAAF;
    }
    
    @media only screen and (max-width: 600px) {
      .email-body_inner,
      .email-footer {
        width: 100% !important;
      }
      
      .content-cell {
        padding: 25px 20px !important;
      }
      
      .button {
        width: 100% !important;
      }
    }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
      <tr>
        <td align="center">
          <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td class="email-masthead">
                <a href="{{website_link}}" class="email-masthead_name">
                  {{website_name}}
                </a>
              </td>
            </tr>
            
            <tr>
              <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                <table class="email-body_inner" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td class="content-cell">
                      {{email_content}}
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            
            <tr>
              <td>
                <table class="email-footer" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td class="content-cell" align="center">
                      <p class="sub align-center">
                        {{copyright}}
                      </p>
                      <p class="sub align-center">
                        <a href="{{website_link}}">{{website_name}}</a>
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
</body>
</html>
