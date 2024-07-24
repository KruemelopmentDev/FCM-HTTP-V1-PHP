# Firebase Cloud Messaging HTTP v1 PHP (no libraries required)

This repository provides a streamlined PHP script for sending push notifications via Firebase Cloud Messaging (FCM) HTTP v1, without external libraries.

## Key Features:

Lightweight: No additional libraries are required, keeping dependencies minimal.
Customizable: Easily modify notification content (title, text, data) to suit your needs.
Optional Bearer Token Caching: Implement a database (not included) to store bearer tokens for optimized performance (consider security implications).
Clear Code Structure: The code is well-structured for readability and maintainability.
Requirements: JSON credentials file from Firebase project (In Firebase Console > Project Settings > Service Accounts > Generate new private key)

## Setup:

### Obtain Credentials:

Create a Firebase project or select an existing one.
Enable the FCM extension for your project.
Download the JSON service account credentials file. Place this file in the same directory as the PHP script (or adjust the path in the script).

### Modify Script:

Update the string of the $serviceAccountData variable with the path to your JSON credentials file.
Replace the {project-id} in the URL of the sendFCMMessage(...) function to your project ID.

### Execute Script:

Integrate the script in your PHP and just call sendNotification(); and passing the parameters.
title = Notification Title
text = Notification Message
topic = Topic to which the message should be send
ttl = TimeToLive in Seconds (max. is 4 weeks)
priority = Priority of the Notification

## Return Value:

The function will return the "message_id" of the notification if send, otherwise it will return an "error", optionally an "error_description" and "where" to know where the problem is.

### Additional Notes:

#### Bearer Token Management:

The provided script doesn't handle bearer token storage. Consider implementing a caching mechanism using a database if you anticipate frequent notifications. Be aware of security considerations when storing tokens. The tokens are valid for 1 hour after generation. ($accessToken["expiresIn"] tells when the token expires.)

#### Further Customization:

Explore FCM documentation (https://firebase.google.com/docs/cloud-messaging) for additional options you can integrate, such as device targeting, and more.

### Contributing:

I welcome contributions to improve this script! Feel free to submit pull requests with enhancements, bug fixes, or additional features.

## License:

Copyright (c) 2024, Krümelopent Dev aka Tom Lukas Grieme
All rights reserved.

This source code is licensed under the APACHE 2.0 license found in the
LICENSE file in the root directory of this source tree.
