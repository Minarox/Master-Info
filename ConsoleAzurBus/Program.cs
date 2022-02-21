﻿using System;
using System.Threading.Tasks;
using Azure.Messaging.ServiceBus;

namespace ConsoleAzurBus
{
    class Program
    {
        // connection string to your Service Bus namespace
        static string connectionString = "Endpoint=sb://dev734-g5.servicebus.windows.net/;SharedAccessKeyName=RootManageSharedAccessKey;SharedAccessKey=Vqx0yyhJT7kTVxhgdk9mvDIg3nvV3u0dVC6wmSPf2gI=";

        // name of your Service Bus queue
        static string queueStock = "msgstock";
        static string queueBusArrival = "busarrive";
        static string queueBusDeparture = "busdepart";

        // the client that owns the connection and can be used to create senders and receivers
        static ServiceBusClient client;

        // the sender used to publish messages to the queue
        static ServiceBusSender sender;

        static async Task Main()
        {
            Console.WriteLine("------------------------------------------------------------------------");
            Console.WriteLine("Welcome to the inventory management interface, what do you want to do ? ");
            Console.WriteLine("");
            Console.WriteLine("1 : Signal the departure of a truck ");
            Console.WriteLine("2 : Signal the arrival of a truck ");
            Console.WriteLine("3 : Send a quantity in stock for a given reference ");
            Console.Write("Please enter one of the numbers corresponding to a proposal (exemple: 2) : ");
            string recover_number = Console.ReadLine();
            int number = int.Parse(recover_number);

            if (number == 1)
            {
                Console.WriteLine("");
                Console.WriteLine("We have reported the departure of a truck");
                Console.WriteLine("");
                send_message(queueBusDeparture);
                execute_main();
            }
            else if (number == 2)
            {
                Console.WriteLine("");
                Console.WriteLine("we have reported the arrival of a truck");
                Console.WriteLine("");
                send_message(queueBusArrival);
                execute_main();
            }
            else if (number == 3)
            {
                Console.WriteLine("");
                Console.WriteLine("Enter the product reference : ");
                string recover_reference = Console.ReadLine();
                int reference = int.Parse(recover_reference);
                Console.WriteLine("");
                Console.WriteLine("Enter the quantity : ");
                string recover_quantity = Console.ReadLine();
                int quantity = int.Parse(recover_quantity);
                Console.WriteLine("");
                execute_main();
            } else
            {
                Console.WriteLine("");
                Console.WriteLine("Wrong number");
                Console.WriteLine("");
                Main();
            }            
        }

        static void execute_main()
        {
            Console.WriteLine("Do you want to make other thing or you want to close the application ?");
            Console.WriteLine("1 : Return to the menu ");
            Console.WriteLine("2 : Close the console ");

            Console.Write("Please enter one of the numbers corresponding to a proposal (exemple: 2) : ");
            string recover_number = Console.ReadLine();
            int number = int.Parse(recover_number);

            if(number == 1)
            {
                Console.WriteLine("");
                Console.Clear();
                Main();
            }
            else
            {
                Console.ReadKey();
            }
        }

        static async Task send_message(string queueDestination)
        {
            // The Service Bus client types are safe to cache and use as a singleton for the lifetime
            // of the application, which is best practice when messages are being published or read
            // regularly.
            //
            // Create the clients that we'll use for sending and processing messages.
            client = new ServiceBusClient(connectionString);
            sender = client.CreateSender(queueDestination);

            // create a batch 
            using ServiceBusMessageBatch messageBatch = await sender.CreateMessageBatchAsync();

            for (int i = 1; i <= 1; i++)
            {
                // try adding a message to the batch
                if (!messageBatch.TryAddMessage(new ServiceBusMessage($"Message {i}")))
                {
                    // if it is too large for the batch
                    throw new Exception($"The message {i} is too large to fit in the batch.");
                }
            }

            try
            {
                // Use the producer client to send the batch of messages to the Service Bus queue
                await sender.SendMessagesAsync(messageBatch);
                Console.WriteLine($"A batch of 1 messages has been published to the queue.");
            }
            finally
            {
                // Calling DisposeAsync on client types is required to ensure that network
                // resources and other unmanaged objects are properly cleaned up.
                await sender.DisposeAsync();
                await client.DisposeAsync();
            }

            Console.WriteLine("Press any key to end the application");
            Console.ReadKey();
        }

        static async Task send_message(string queueDestination, string quantite, string reference)
        {
            // Because ServiceBusClient implements IAsyncDisposable, we'll create it 
            // with "await using" so that it is automatically disposed for us.
            await using var client = new ServiceBusClient(connectionString);

            // The sender is responsible for publishing messages to the queue.
            ServiceBusSender sender = client.CreateSender(queueDestination);
            ServiceBusMessage quantiteSend = new ServiceBusMessage(quantite);
            ServiceBusMessage referenceSend = new ServiceBusMessage(reference);

            await sender.SendMessageAsync(quantiteSend);
            await sender.SendMessageAsync(referenceSend);

            // The receiver is responsible for reading messages from the queue.
            ServiceBusReceiver receiver = client.CreateReceiver(queueDestination);
            ServiceBusReceivedMessage receivedMessage = await receiver.ReceiveMessageAsync();

            string body = receivedMessage.Body.ToString();
            Console.WriteLine(body);

            Console.WriteLine("Press any key to end the application");
            Console.ReadKey();
        }

    }
}