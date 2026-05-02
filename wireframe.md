wireframes.md
# UI Wireframes and User Flows for Nepal Bulletin Board

## Introduction to UI Wireframes

The UI wireframes for the Nepal Bulletin Board Online News Portal have been designed using a mobile-first approach, recognizing that the majority of Nepali news consumers access content through smartphones rather than desktop computers. The design philosophy prioritizes speed, simplicity, and credibility above all else. Every screen is optimized for fast loading times, easy navigation, and a clear visual hierarchy that guides users naturally toward the most important content. The wireframes described below represent low-fidelity layouts that focus on structure, component placement, and user interactions rather than colors, fonts, or visual styling. These wireframes will serve as the blueprint for frontend development, ensuring that all team members have a shared understanding of how each page should look and behave.

## Homepage Wireframe

The homepage serves as the primary landing page for all visitors to the Nepal Bulletin Board. The layout follows a single vertical scroll structure. At the very top of the page is a sticky header that remains visible even when the user scrolls down. This header contains the site logo positioned on the left side, a hamburger menu icon on the right side for mobile devices, and a search icon placed immediately next to the hamburger menu. When users tap the hamburger menu, a slide-out sidebar appears from the left edge of the screen, displaying navigation links to all major categories including Politics, Business, Sports, Entertainment, and Technology, along with links to the user's profile dashboard and logout option if the user is logged in.

Directly below the sticky header is a breaking news ticker that scrolls horizontally across the full width of the screen. This ticker automatically cycles through the five most recent urgent news headlines, with each headline moving into view every five seconds. The ticker has a red background with white text to draw immediate attention. Users can tap any headline in the ticker to navigate directly to that article's detail page.

Below the breaking news ticker is the hero section, which displays the three most important stories of the day. Each hero story is represented by a large featured image that spans the full width of the screen. The headline and a brief one-sentence summary are overlaid on the bottom portion of the image with a semi-transparent dark gradient background to ensure text readability. Users can tap anywhere on the hero story card to open the full article. A small indicator at the bottom of the hero section shows which of the three stories is currently visible, and users can swipe left or right to cycle through them.

Following the hero section, the page is organized into category rows. Each category row corresponds to a news category such as Politics, Business, Sports, Entertainment, and Technology. Each row displays a horizontal scrolling list of article cards. Every article card contains a small thumbnail image on the left side, a headline truncated to two lines of text, the publication date displayed in a relative format such as "2 hours ago," and a bookmark icon on the top right corner that registered users can tap to save the article for later reading. Users can scroll left or right within each row to see more articles beyond the initial three to four cards visible on screen. Each category row also has a "View All" link positioned at the top right corner of the row header, which navigates to the full category page showing all articles in that category.

At the bottom of the homepage is a footer section. The footer contains links to static pages including About Us, Contact, Privacy Policy, and Terms of Use. Additionally, the footer contains a newsletter subscription input field with a placeholder text reading "Enter your email address" and a subscribe button. The entire homepage implements lazy loading for images, meaning that images below the visible screen area are only loaded when the user scrolls down to them, which significantly improves initial page load time.

## Article Detail Page Wireframe

The article detail page is where users read full news stories. The page begins with the same sticky header and breaking news ticker found on the homepage, ensuring consistent navigation throughout the portal. Below the ticker, the article headline is displayed in a large, bold font with a font size of 28 pixels on mobile devices and 42 pixels on desktop screens. The headline is followed by metadata displayed on a single line, including the publication date in a full format such as "April 18, 2026," the author's full name, and an estimated reading time calculated automatically based on the word count of the article.

Directly below the metadata is a social sharing bar containing icon buttons for Facebook, Twitter, WhatsApp, and a copy link button. When users tap any of the sharing buttons, the device's native share dialog opens, allowing users to share the article link through their preferred messaging or social media application. If the user taps the copy link button, a brief notification appears at the bottom of the screen confirming that the link has been copied to the clipboard.

Below the sharing bar is the featured image, displayed at full width of the screen. If the image has a caption, that caption appears in small gray text directly below the image. The article body follows the featured image, using a serif font for improved readability of long-form text. The font size is set to 18 pixels, line height to 1.6, and margins are applied to ensure text does not touch the edges of the screen. Throughout the article body, any inline related article links are displayed as highlighted text in the portal's primary color. Tapping these links navigates the user to the related article without losing their place in the current article.

At the bottom of the article body, a comment section is displayed. For users who are logged in, a text input area appears with a placeholder reading "Write your comment here," along with a submit button. For users who are not logged in, a message appears prompting them to log in or register to leave a comment, with clickable links for both actions. Below the comment input area, existing comments are displayed in a vertical list. Each comment card shows the commenter's name, their profile picture if they have uploaded one, a timestamp showing when the comment was posted, the comment text itself, and an upvote or like button. Comments are sorted by the number of upvotes by default, with the most upvoted comments appearing at the top. Users can change the sort order to newest first by tapping a sort button.

Below the comment section is a "You Might Also Like" section that displays between three and five related articles. These recommendations are generated based on the current article's category and tags. Each recommended article card includes a small thumbnail image, the headline, and the publication date. Tapping any of these cards navigates the user to that article.

## Category Page Wireframe

The category page displays all articles belonging to a specific category, such as Politics or Sports. The page begins with the same sticky header and breaking news ticker. Below the ticker, a large category title is displayed, followed by a brief description of the category. Below the description, a filter bar contains three buttons for sorting options: Newest First, Oldest First, and Most Popular. The currently active sort option is highlighted.

Below the filter bar, articles are displayed in a vertical list. Each article occupies a full-width card. The card layout includes a thumbnail image on the left side occupying approximately 30 percent of the card width, with the headline and a two-line summary occupying the remaining 70 percent on the right. The publication date appears below the summary. As the user scrolls down toward the bottom of the page, additional articles are loaded automatically using infinite scroll, eliminating the need for pagination buttons. A loading indicator appears briefly while new articles are being fetched from the server. At the bottom of the page, the same footer from the homepage is displayed.

## Search Results Page Wireframe

The search results page displays articles that match a user's search query. The page layout includes the sticky header, but unlike other pages, the search bar is also displayed prominently at the top of the main content area. The search bar is pre-filled with the user's search term. Below the search bar, a line of text displays the number of results found, formatted as "12 results found for 'earthquake'."

If no results are found for the search query, a friendly message is displayed instead, suggesting that the user try different keywords or browse categories instead. The message includes a link to the homepage and links to each major category. If results are found, they are displayed in the same vertical list format as the category page. One key difference is that matching keywords within each result's headline and summary are highlighted in bold or with a yellow background to help users quickly identify why each article matched their search. The same infinite scroll behavior applies as the user scrolls down.

## Login and Registration Screens Wireframe

The login screen is a simple, focused page with no header navigation to minimize distractions. A centered card contains the login form. The form includes an email address input field, a password input field, a login button, a "Forgot Password" link displayed below the login button, and a link to the registration page for new users. Below the card, a small link returns users to the homepage if they do not wish to log in.

The registration screen follows a similar layout but includes additional fields. The form contains fields for full name, email address, password, and confirm password. A checkbox is included for users to agree to the terms and conditions, with the words "terms and conditions" linked to the full terms document. A register button submits the form. Both screens include real-time validation. As the user types in each field, validation messages appear below the field if the input is invalid. For example, if the email address does not contain an @ symbol, a red message appears saying "Please enter a valid email address." If the password is fewer than eight characters, a message appears saying "Password must be at least 8 characters." After successful login or registration, the user is automatically redirected to the page they were previously viewing, or to the homepage if they arrived directly at the login screen.

## User Profile Dashboard Wireframe

The user profile dashboard is accessible only to logged-in users. The page begins with the same sticky header. Below the header, a profile header displays the user's full name, email address, and a circular profile picture placeholder. An upload button below the profile picture allows users to upload a new image. When the upload button is tapped, the device's file picker opens, and after an image is selected, it is cropped to a square and uploaded to the server.

Below the profile header, three tabs are displayed horizontally: Saved Articles, My Comments, and Subscription Preferences. The Saved Articles tab displays a list of all articles the user has bookmarked. Each article in this list shows a thumbnail image, headline, publication date, and a remove button. Tapping the remove button deletes the article from the user's saved list without requiring confirmation. Tapping the article itself navigates to the article detail page.

The My Comments tab displays a list of all comments the user has made across all articles. Each comment entry shows the comment text, the title of the article it was posted on (which is a clickable link), the date the comment was posted, and the current moderation status. Moderation status can be pending, approved, or rejected. If a comment is rejected, a reason is displayed below the comment text.

The Subscription Preferences tab contains a toggle switch for daily newsletter subscription and a separate toggle switch for breaking news push notifications. Both toggles are initially set based on the user's current preferences. When the user changes a toggle, a small saving indicator appears briefly, and the new preference is sent to the server.

## Journalist Dashboard Wireframe

The journalist dashboard is accessible only to users assigned the journalist role. The page layout includes the sticky header and a sidebar navigation menu. On desktop screens, the sidebar is permanently visible on the left side. On mobile devices, the sidebar collapses into a bottom navigation bar with icons. The main content area displays a table or card list of the journalist's own articles. Each article entry shows the article title, current status (draft, pending review, published, or rejected), submission date, and action buttons. The action buttons include Edit for drafts, View for published articles, and Delete for any article not yet published.

A large floating action button is displayed at the bottom right corner of the screen. This button is circular and contains a plus icon. Tapping this button opens the article editor screen. The article editor screen contains a headline input field, a category dropdown menu populated with all available categories, a featured image upload area that accepts both drag-and-drop and traditional file selection, a rich text editor with formatting buttons for bold, italic, headings, lists, and image insertion, a tags input field where users can type tags separated by commas, and a submit button. When the journalist taps the submit button, the article status changes to pending review, and an email notification is automatically sent to all editors.

## Editor Dashboard Wireframe

The editor dashboard is accessible only to users with the editor or admin role. The layout is similar to the journalist dashboard, but the main content area displays an approval queue table. This table shows all articles currently pending review. Each row shows the article title, the submitting journalist's name, the submission date, and action buttons. The action buttons include Preview, Approve, Request Revisions, and Reject.

When the editor taps the Preview button, a modal dialog opens displaying the full article exactly as it will appear once published. The editor can read the entire article within this modal. At the bottom of the preview modal, the same Approve, Request Revisions, and Reject buttons appear. When the editor taps Approve, a confirmation dialog appears asking "Are you sure you want to approve this article?" Upon confirmation, the article status changes to published, the article becomes visible on the frontend, and the journalist receives an email notification. When the editor taps Request Revisions, a modal dialog appears with a text area for revision notes. The editor enters specific feedback and taps Send. The article status changes back to draft, and the journalist receives an email with the revision notes. When the editor taps Reject, a modal dialog appears with a text area for rejection reason. Upon submission, the article status changes to rejected, and the journalist receives an email explaining the rejection.

## User Interaction Flows

### Visitor to Registered User Flow

The visitor to registered user flow begins when a visitor attempts to comment on an article. The frontend checks for an authentication token stored in the browser's local storage. Since no token exists, the system displays a modal dialog with two buttons: Login and Register. The user taps Register, which navigates to the registration screen. The user fills in all required fields. As the user types, frontend validation checks the email format, password length, and password confirmation match. When the user taps the Register button, the frontend sends a POST request to the backend API. If the email already exists, an error message appears. If registration is successful, the user receives a verification email. After clicking the verification link, the user is automatically logged in and redirected back to the article they were originally viewing. The comment input area is now visible and enabled.

### Article Reading and Commenting Flow

The article reading and commenting flow begins when a user taps on an article card from any page. The frontend navigates to the article detail page and simultaneously fetches the full article data. While loading, a skeleton loader is displayed to indicate that content is loading. Once the data arrives, the frontend renders the article. As the user scrolls down, the frontend tracks scroll depth. When the user reaches 70 percent of the article, an analytics event is sent to record a read. If the user taps the bookmark icon, the article is saved to their account. If the user types a comment and taps submit, the comment is immediately displayed as pending moderation with a gray background. Once the backend confirms the comment has been saved, the status indicator updates.

### Journalist Article Submission Flow

The journalist article submission flow begins when a journalist logs in and navigates to their dashboard. The journalist taps the floating action button to create a new article. The journalist enters the headline, selects a category, uploads a featured image, writes the content using the rich text editor, and adds tags. The frontend automatically saves the draft to local storage every thirty seconds as a backup. When the journalist taps Submit for Review, validation checks ensure all required fields are filled. If validation passes, the article is sent to the backend with status set to pending review. Upon success, the journalist returns to the dashboard, and the article appears in the list with status pending review.

### Editor Approval Flow

The editor approval flow begins when an editor logs in and navigates to their dashboard. The frontend fetches the list of pending articles. The editor taps on an article to preview it. After reviewing, the editor taps Approve, Request Revisions, or Reject. If Approve is selected, a confirmation dialog appears. Upon confirmation, the backend updates the article status to published, and the journalist receives an email notification. The article is removed from the approval queue.

### Search Flow

The search flow begins when a user taps the search icon in the sticky header. A full-screen search overlay appears with the keyboard automatically focused. As the user types each character, the frontend waits 300 milliseconds and then sends an API request with the current search term. Results appear in real time below the search input. Tapping a result navigates to the article. Tapping the search button on the keyboard navigates to the full search results page with the search term saved as a URL parameter.

## Responsive Behavior

The frontend detects screen width using CSS media queries. On screens smaller than 768 pixels wide, the hamburger menu icon is displayed, and navigation links are hidden inside a slide-out sidebar. On screens 768 pixels or larger, the hamburger menu is hidden, and all navigation links are displayed horizontally in the header. Category rows on the homepage display three article cards at a time on desktop instead of one and a half on mobile. The article detail page on desktop uses a two-column layout with the article body taking 70 percent of the width and a sidebar on the right displaying related articles and a table of contents. All touch interactions are fully supported on mobile, while mouse hover effects are added for desktop users.

---

**End of UI Wireframes and User Flows Section**
