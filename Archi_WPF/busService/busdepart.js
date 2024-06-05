const { delay, ServiceBusClient, ServiceBusMessage } = require("@azure/service-bus");
const fs = require("fs");

// connection string to your Service Bus namespace
const connectionString = "Endpoint=sb://dev734-g5.servicebus.windows.net/;SharedAccessKeyName=RootManageSharedAccessKey;SharedAccessKey=Vqx0yyhJT7kTVxhgdk9mvDIg3nvV3u0dVC6wmSPf2gI="

// name of the queue
const queueName = "busdepart";

async function main() {
	let array = [];

	// create a Service Bus client using the connection string to the Service Bus namespace
	const sbClient = new ServiceBusClient(connectionString);

	// createReceiver() can also be used to create a receiver for a subscription.
	const receiver = sbClient.createReceiver(queueName);

	// function to handle messages
	const myMessageHandler = async (messageReceived) => {
		console.log(`Received message: ${messageReceived.body}`);
		array.push(messageReceived.body.toString());

		// Open JSON file
		fs.readFile("data/busdepart.json", "utf8" , (err, data) => {
			if (err) {
				console.error(err)
				return
			}

			// Concat old JSON with new values
			let oldJson = JSON.parse(data);
			let newJson = oldJson.concat(array);

			// Write new values in JSON file
			fs.writeFile("data/busdepart.json", JSON.stringify(newJson), err => {
				if (err) {
					console.error(err)
					return
				}
			})
		})
	};

	// function to handle any errors
	const myErrorHandler = async (error) => {
		console.log(error);
	};

	// subscribe and specify the message and error handlers
	receiver.subscribe({
		processMessage: myMessageHandler,
		processError: myErrorHandler
	});

	// Waiting long enough before closing the sender to send messages
	await delay(20000);

	await receiver.close();
	await sbClient.close();
}

// call the main function
main().catch((err) => {
	console.log("Error occurred: ", err);
	process.exit(1);
});