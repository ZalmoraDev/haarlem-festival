## Legal & Accessibility Compliance
### WCAG 2.2 Compliance (Level AA)

[//]: # (TODO: Verify `### WCAG ??? Compliance` before delivering project, and switch to WCAG 2.2 Compliance)
- **Semantic HTML**: Proper use of headings, articles, sections, aria-labels, alt-text ([settings page](/app/Views/Pages/User/settings.php))
- **Color Contrast**: Text colors meet minimum 4.5:1 contrast ratio (See list below)
- **Keyboard Navigation**: All interactive elements (forms, buttons, modals) accessible via keyboard
- **Focus Indicators**: Visible focus states on all interactive elements (Tailwind `focus:`)
- **Error Identification**: Clear error messages via toast notifications (`$_SESSION['flash_errors']`) describing what went wrong
- **Responsive Design**: Tablet- and Mobile-friendly layout adapting to different screen sizes


<details>
<summary><b>WCAG Color Contrast Examples</b></summary>

![WCAG Contrast Example 1](docs/wcag/wcag1.png)     
![WCAG Contrast Example 2](docs/wcag/wcag2.png)     
![WCAG Contrast Example 3](docs/wcag/wcag3.png)     
![WCAG Contrast Example 4](docs/wcag/wcag4.png)
</details>

[//]: # (TODO: Rework `### GDPR Compliance` completely before delivering project, and verify it)

### GDPR Compliance
- **Right of Access**: Users can view their account data (username, email) on
  the [settings page](/app/Views/Pages/User/settings.php)
- **Right to Rectification**: Users can [edit](/app/Serv/UserServ.php) and correct their username and email
- **Right to Erasure**: Users can [delete](/app/Serv/UserServ.php) their account with name confirmation
- **Data Security**: Passwords hashed with bcrypt, secure session
  management,[CSRF](/app/Core/Csrf.php) & [CSP](/app/Core/Csp.php) protection
- **Data Minimization**: Only essential data collected (username, email, password hash) - no tracking or third-party
  data sharing