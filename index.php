<!DOCTYPE html>
<html>

<head>
   <meta charset="utf8">
   <title>Webex Video Call</title>
   <link rel="stylesheet" href="https://code.s4d.io/widget-recents/production/main.css">
   <link rel="stylesheet" href="https://code.s4d.io/widget-space/production/main.css">
   <script src="https://code.s4d.io/widget-space/production/bundle.js"></script>
   <script src="https://code.s4d.io/widget-recents/production/bundle.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<style>
   body {
      margin: 0;
      padding: 0;
      background-image: url('img.jpg');
      background-size: cover;
      background-position: center;
      font-family: Arial, sans-serif;
   }

   .container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
   }

   .scheduled-meetings {
      margin-top: 20px;
      padding: 10px;
      background-color: #f0f0f0;
   }
</style>

<body>

   <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">

      <div id="recents" style="width: 300px; height: 500px;"></div>
      <div id="space" style="width: 750px; height: 500px;"></div>
      <div id="scheduledMeetingsRecents" class="scheduled-meetings"></div>
      <div id="scheduledMeetingsSpace" class="scheduled-meetings"></div>

      <script>
         const userToken = prompt("Please enter your Webex token:");

         if (!userToken) {
            alert("Token is required. Please refresh the page and enter a valid token.");
         } else {
            const recentsElement = document.getElementById('recents');
            const spaceElement = document.getElementById('space');

            webex.widget(recentsElement).recentsWidget({
               accessToken: userToken,
               onEvent: callback
            });

            function callback(type, event) {
               if (type === "rooms:selected") {
                  const selectedRoom = event.data.id;

                  try {
                     webex.widget(spaceElement).remove().then(function (removed) {
                        if (removed) {
                           console.log('Removed existing Space widget!');
                        }
                     });
                  } catch (err) {
                     console.error('Could not remove Space widget: ', err);
                  }

                  webex.widget(spaceElement).spaceWidget({
                     accessToken: userToken,
                     destinationType: "spaceId",
                     destinationId: selectedRoom,
                     activities: { "files": true, "meet": true, "message": true, "people": true },
                     initialActivity: 'message',
                     secondaryActivitiesFullWidth: false
                  });
                  fetchRoomInformation(userToken, selectedRoom);
               }
            }

            async function fetchRoomInformation(token, roomId) {
               try {
                  const response = await axios.get(`https://api.ciscospark.com/v1/rooms/${roomId}`, {
                     headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                     },
                  });

                  const roomData = response.data;
                  console.log('Room Information:', roomData);
                  simulateParticipantManagement(token, roomId, roomData.title);
                  scheduleMeeting(roomData.title);
                  viewScheduledMeetings('scheduledMeetingsRecents');
               } catch (error) {
                  console.error('Error fetching room information:', error);
               }
            }

            function simulateParticipantManagement(token, roomId, roomTitle) {
               console.log(`Simulating participant management for room: ${roomTitle}`);
            }

            function scheduleMeeting(roomTitle) {
               const meetingTitle = prompt("Enter the meeting title:");
               const meetingDate = prompt("Enter the meeting date (YYYY-MM-DD):");
               const meetingTime = prompt("Enter the meeting time (HH:MM AM/PM):");

               console.log('Meeting Title:', meetingTitle);
               console.log('Meeting Date:', meetingDate);
               console.log('Meeting Time:', meetingTime);

               if (meetingTitle && meetingDate && meetingTime) {
                  const meetingDetails = {
                     title: meetingTitle,
                     date: meetingDate,
                     time: meetingTime,
                     room: roomTitle,
                  };

                  console.log('Meeting Scheduled:', meetingDetails);
                  storeScheduledMeeting(meetingDetails);
               } else {
                  console.log('Meeting scheduling cancelled or incomplete.');
               }
            }

            function storeScheduledMeeting(meetingDetails) {
               const existingMeetings = JSON.parse(localStorage.getItem('scheduledMeetings')) || [];
               existingMeetings.push(meetingDetails);
               localStorage.setItem('scheduledMeetings', JSON.stringify(existingMeetings));
            }

            function viewScheduledMeetings(elementId) {
               const scheduledMeetings = JSON.parse(localStorage.getItem('scheduledMeetings')) || [];
               const element = document.getElementById(elementId);

               if (scheduledMeetings.length > 0) {
                  let meetingsHTML = '<h3>Scheduled Meetings:</h3><ul>';

                  scheduledMeetings.forEach((meeting, index) => {
                     meetingsHTML += `<li>${index + 1}. ${meeting.title} - Date: ${meeting.date}, Time: ${meeting.time}, Room: ${meeting.room}</li>`;
                  });

                  meetingsHTML += '</ul>';
                  element.innerHTML = meetingsHTML;
               } else {
                  element.innerHTML = '<p>No scheduled meetings.</p>';
               }
            }
         }
      </script>

   </div>

</body>

</html>
