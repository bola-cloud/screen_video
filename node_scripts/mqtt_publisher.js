const mqtt = require('mqtt');
const brokerIP = 'mqtt://77.37.54.128:1883'; // Update with your broker IP
const client = mqtt.connect(brokerIP);

// Data passed from Laravel
const data = process.argv[2]; // JSON data with video link and ad info

client.on('connect', function () {
    console.log('Connected to MQTT broker');

    // Parse the data received from Laravel
    const adData = JSON.parse(data);

    // Publish the ad to the TV's topic (tv/1/play_ad)
    const topic = `tv/${adData.tv_id}/play_ad`;
    const message = JSON.stringify({
        video_link: adData.video_link,
        advertisement_id: adData.advertisement_id
    });

    client.publish(topic, message, function (err) {
        if (err) {
            console.error('Failed to publish message:', err);
        } else {
            console.log(`Ad published to topic ${topic}: ${message}`);
        }

        client.end();
    });
});
