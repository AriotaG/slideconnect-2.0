# slideconnect-2.0

SliderConnect 2.0 Project Summary - Streaming Management Software

Main Objective
The software is designed to manage and monitor real-time video streams through multiple virtual "doors". Each door represents a single access point for video streaming, allowing centralized and customized management of video content.

Key Features

1. Door Management:
- Creation and Configuration: Allows administrators to create virtual doors, assign names, locations and descriptions to them.
- Connection between Doors: Facilitates connection and integration between different doors, allowing distribution of video streams through multiple access points.

2. Video Streaming:
- Custom Video.js Players: Implements Video.js players with CSS customizations to ensure a consistent and functional look across all doors.
- Logo Overlay: Allows you to upload and apply custom logos to videos, improving branding and recognition.

3. Schedule Management:
- Program Creation and Editing: Allows you to plan video programs, specifying titles, descriptions, start and end times.
- FullCalendar Integration: Displays the program schedule in an interactive calendar, making it easy to plan and manage video events.

4. Advertising Content Injection:
- Commercial Management: Automatically inserts video advertisements into the main stream based on the configured schedule.
- Main Stream Resume: After playing a commercial, the system automatically returns to the main stream.

5. Administrative Interface:
- Intuitive Dashboard: Offers a user-friendly dashboard for administrators, allowing easy navigation and management of ports, schedules and video settings.
- Logo Upload and Management: Provides tools to upload, update and manage the logos displayed on videos.

6. Dynamic Configurations:
- External CSS Files: Centralizes CSS customizations in external files to improve site maintenance and performance.
- CSS Variable Management: Use CSS variables to dynamically manage elements such as logo overlays, ensuring flexibility and ease of updating.

Software Benefits

- Scalability: Facilitates the addition of new ports and the management of a growing number of video streams without compromising performance.
- Maintainability: Centralized CSS and the modular structure of the code simplify maintenance and future updates.
- Customization: Offers extensive customization options for video players, allowing companies to maintain a strong brand identity.
- Automation: Automates the insertion of advertising content, optimizing the monetization of video streams.

Technologies Used

- PHP: For server-side management and integration of dynamic features.
- Video.js: As the main video player, customized through CSS and JavaScript.
- FullCalendar: For viewing and managing the schedule in a calendar format.
- Bootstrap and AdminLTE: For styling and creating a responsive and modern administrative interface.


SlideConnect WebRTC 2.0

Follow the following steps for proper installation:

1) download a free version of AdminLTE (for policy issues you cannot include AdminLTE files so you will find the adminlte folder empty which you will need to fill with the free download at the link: https://adminlte.io/)
2) Open the ReadMe folder, you will find the database and project schema.
3) The config/config.php file must be configured with your DB parameters.
4) also in the config/confg.php file you will need to enter the domain name or path you are using.
5) Username and Password in ReadMe/credential.txt

As of the current status of 10/01/2025, the WebRTC link has not been connected yet. it is only 24 hours since we started development. :)
