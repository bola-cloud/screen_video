import mqtt from 'mqtt';

// Replace this with your broker IP and port
const brokerIP = 'mqtt://77.37.54.128:1883';
const client = mqtt.connect(brokerIP);

// Get data passed from Laravel
const data = process.argv[2]; // Expecting JSON data with video link and ad info

// Event listener for connection success
client.on('connect', function () {
    console.log('Connected to MQTT broker');

    try {
        // Parse the data received from Laravel
        const adData = JSON.parse(data);

        // Check that the required fields exist in the parsed data
        if (!adData.tv_id || !adData.video_link) {
            throw new Error('Invalid data: Missing tv_id or video_link');
        }

        // Construct the topic dynamically based on the TV ID
        const topic = `tv/${adData.tv_id}/play_ad`;
        
        // Construct the message to include relevant ad info (like video link and ad id)
        const message = JSON.stringify({
            video_link: adData.video_link,
            ad_id: adData.ad_id,
            duration: adData.duration, // If you want to include the duration as well
        });

        // Publish the message to the specified topic
        client.publish(topic, message, function (err) {
            if (err) {
                console.error('Failed to publish message:', err);
            } else {
                console.log(`Ad published to topic ${topic}: ${message}`);
            }

            // Close the MQTT connection after publishing the message
            client.end();
        });

    } catch (err) {
        // Handle parsing errors or issues with the data format
        console.error('Error parsing or publishing the data:', err.message);
        client.end(); // Ensure the client is closed even in case of errors
    }
});

// Event listener for connection errors
client.on('error', function (err) {
    console.error('Failed to connect to MQTT broker:', err);
    client.end(); // Ensure the client is closed on connection error
});
