<?php
return [
    'system' => [  
        'success' => 'Operation successful',  
        'fail' => 'Operation failed',  
        'error' => 'System error',  
        'forbidden' => 'Access forbidden',  
        'sendOK' => 'Sent successfully',  
        'sendFail' => 'Sending failed',  
        'delOk' => 'Deletion successful',  
        'settingOk' => 'Settings updated successfully',  
        'notNull' => 'Cannot be empty',  
        'editOk' => 'Edit successful',  
        'editFail' => 'Edit failed',  
        'addOk' => 'Addition successful',  
        // The original 'addFail' seems to be a typo, assuming it should be '添加失败' which translates to 'Addition failed'  
        'addFail' => 'Addition failed',  
        'joinOk' => 'Joining successful',  
        'notAuth' => 'You do not have the permission to perform this operation!',  
        'demoMode' => 'Modifications are not supported in demo mode',  
        'parameterError' => 'Parameter error',  
        'longTime' => 'Request timeout',  
        'apiClose' => 'API is closed',  
        'appIdError' => 'appId error',  
        'signError' => 'Signature error',  
        'tooFast'=>"You visited too fast！"
    ],
    'messageType' => [  
        'other' => "[Unsupported message type]",  
        'image' => "[Image]",  
        'voice' => "[Voice]",  
        'video' => "[Video]",  
        'file' => "[File]",  
        'webrtcAudio' => "[Audio call request with you]",  
        'webrtcVideo' => "[Video call request with you]",  
    ],
    'friend' => [  
        'notAddOwn' => "You cannot add yourself as a friend",  
        'already' => "You are already friends",  
        'repeatApply' => "You have already sent a request, please wait for the other person to accept",  
        'new' => "New friend",  
        'apply' => "Has added you as a friend",  
        'notApply' => "The request does not exist",  
        'not' => "Friend does not exist",  
    ],  
    'group' => [  
        'name' => "Group chat",  
        'notAuth' => "You do not have permission to perform this action. Only the group owner and administrators can make changes!",  
        'userLimit' => "The number of members cannot exceed {:userMax} people!",  
        'invite' => "{:username} has invited you to join the group chat",  
        'add' => "{:username} has created a group chat",  
        'join'=>"{:username} join the group chat",
        'atLeast' => "Please select at least two people!",  
        'alreadyJoin' => "You are already in this group!",  
        'exist' => "The group chat does not exist",
        'notice'=>"Announcement",
        'all'=>"All",
    ],
    'user' => [  
        'exist' => "User does not exist",  
        'codeErr' => "Verification code is incorrect!",  
        'newCodeErr' => "New verification code is incorrect!",  
        'passErr' => "Original password is incorrect!",  
        'already' => "Account already exists",  
        'registerOk' => "Registration successful",  
        'loginOk' => "Login successful",  
        'tokenFailure' => "TOKEN has expired!",  
        'forbid' => "Your account has been disabled",  
        'passError' => "Password is incorrect",  
        'logoutOk' => "Logout successful!",  
        'closeRegister' => "The system has disabled registration!",  
        'inviteCode' => "Invite code has expired!",  
        'accountVerify' => "Account must be a phone number or email",  
        'waitMinute' => "Please try again after one minute!",  
        "loginAccount" => "Login account",  
        "registerAccount" => "Register account",  
        "editPass" => "Change password",  
        "editAccount" => "Edit account",  
        'loginError' => 'Login information is incorrect. Please log in again.',  
        'mustToken' => 'Please log in to the system first',  
        'blacklist' => 'Login has expired. Please log in again',  
        'expired' => 'Login has expired. Please log in again',
        'notOwn' =>"Customer service can't be for him",
        'registerLimit'=>"Please register again in {:time} minutes"
    ],  
    'im' => [  
        'forbidChat' => "Private chatting is currently prohibited!",  
        'notFriend' => "You are not on their friend list, cannot send messages!",  
        'friendNot' => "They are not your friend, cannot send messages!",  
        'forwardLimit' => "Please select fewer than {:count} recipients for forwarding!",  
        'exist' => "Message does not exist",  
        'forwardRule' => "Forwarding failed for {:count} messages due to rule restrictions!",  
        'forwardOk' => 'Message forwarded successfully',  
        'you' => 'You',  
        'other' => 'Recipient',  
        'redoLimitTime' => "Cannot recall messages after {:time} minutes!",  
        'redo' => "A message has been recalled",  
        'manageRedo' => "A message has been recalled by (an admin)",
    ],
    'webRtc' => [  
        'cancel' => 'Call has been canceled',  
        'refuse' => 'Call has been rejected',  
        'notConnected' => 'Call not connected',  
        'duration' => 'Call duration: {:time}',  
        'busy' => 'Busy',  
        'other' => 'Operation performed on another device',  
        'video' => 'Video call',  
        'audio' => 'Audio call',  
        'answer' => 'Answer call request',  
        'exchange' => 'Data exchange in progress',  
        'fail' => 'Call failed',  
    ],  
    'email' => [  
        'input' => 'Please enter a valid email address',  
        'testTitle' => "Test Email",  
        'testContent' => "This is a test email. If you receive it, it means all your configurations are correct!",  
    ],  
    'task' => [  
        'schedule' => 'Scheduled Task',  
        'queue' => 'Message Queue',  
        'worker' => 'Message Push',  
        'clearStd' => 'Clear Logs',  
        'null' => "Unknown task",  
        'winRun' => "To start on Windows, please run the 'start_for_win.bat' file in the root directory",  
        'alreadyRun' => "Process is already running",  
        'startOk' => "Started successfully",  
        'startFail' => "Failed to start",  
        'notRun' => "Process is not running",  
        'logExist' => "Log does not exist",  
    ],  
    'file' => [  
        'preview' => "Preview file",  
        'browserDown' => "Please use the browser to download",  
        'exist' => "File preview", // Note: This might be a duplicate of 'preview' and could be replaced with a more specific message  
        'uploadLimit' => "File size cannot exceed {:size}MB",  
        'typeNotSupport' => "File format is not supported",  
        'uploadOk' => "Upload successful",  
    ],  
    'scan' => [  
        'failure' => 'QR code has expired'  
    ]
];