using System;
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

        static async Task Main()
        {
            Console.WriteLine("|------------------------------------------------------------------------|");
            Console.WriteLine("|Welcome to the inventory management interface, what do you want to do ? |");
            Console.WriteLine("|------------------------------------------------------------------------|");
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
                await send_message(queueBusDeparture, "Depart");
                execute_main();
            }
            else if (number == 2)
            {
                Console.WriteLine("");
                Console.WriteLine("we have reported the arrival of a truck");
                Console.WriteLine("");
                await send_message(queueBusDeparture, "Arrive");
                execute_main();
            }
            else if (number == 3)
            {
                Console.WriteLine("");
                Console.WriteLine("Enter the product reference : ");
                string recover_reference = Console.ReadLine();
                Console.WriteLine("");
                Console.WriteLine("Enter the quantity : ");
                string recover_quantity = Console.ReadLine();
                int quantity = int.Parse(recover_quantity);
                Console.WriteLine("");
                await send_message(queueStock, recover_quantity, recover_reference);
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
                Console.WriteLine("Press any key to end the application");
                Console.ReadKey();
            }
        }

        static async Task send_message(string queueDestination, string message)
        {
            // Because ServiceBusClient implements IAsyncDisposable, we'll create it 
            // with "await using" so that it is automatically disposed for us.
            await using var client = new ServiceBusClient(connectionString);

            // The sender is responsible for publishing messages to the queue.
            ServiceBusSender sender = client.CreateSender(queueDestination);
            ServiceBusMessage quantiteSend = new ServiceBusMessage(message);

            await sender.SendMessageAsync(quantiteSend);

            // The receiver is responsible for reading messages from the queue.
            ServiceBusReceiver receiver = client.CreateReceiver(queueDestination);
            ServiceBusReceivedMessage receivedMessage = await receiver.ReceiveMessageAsync();

            string body = receivedMessage.Body.ToString();
            Console.WriteLine(body);

            Console.WriteLine("");
        }

        static async Task send_message(string queueDestination, string quantite, string reference)
        {
            // Because ServiceBusClient implements IAsyncDisposable, we'll create it 
            // with "await using" so that it is automatically disposed for us.
            await using var client = new ServiceBusClient(connectionString);

            // The sender is responsible for publishing messages to the queue.
            ServiceBusSender sender = client.CreateSender(queueDestination);
            ServiceBusMessage referenceSend = new ServiceBusMessage("Reference : " + reference + ", Stock : " + quantite);

            await sender.SendMessageAsync(referenceSend);

            // The receiver is responsible for reading messages from the queue.
            ServiceBusReceiver receiver = client.CreateReceiver(queueDestination);
            ServiceBusReceivedMessage receivedMessage = await receiver.ReceiveMessageAsync();

            string body = receivedMessage.Body.ToString();
            Console.WriteLine(body);

            Console.WriteLine("");
        }

    }
}