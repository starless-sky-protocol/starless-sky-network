using System;
using System.Net.Http;
using System.Text;
using StarlessSkyNetworkTester.Tests;
using Newtonsoft.Json;
using System.Collections.Generic;
using System.Diagnostics;

#nullable enable

namespace StarlessSkyNetworkTester
{
    internal class Program
    {
        static StringBuilder output = new StringBuilder();
        public static long LastRequestElapsed = 0;
        public static StringContent AsJson(object? o) => new StringContent(o == null ? "{}" : JsonConvert.SerializeObject(o), Encoding.UTF8, "application/json");

        public static dynamic JSONRequest(HttpMethod method, string url, object? data)
        {
            var httpClient = new HttpClient();
            httpClient.Timeout = TimeSpan.FromSeconds(3);
            var message = new HttpRequestMessage(method, url);
            message.Content = AsJson(data);

            Stopwatch sw = new Stopwatch(); sw.Start();
            var response = httpClient.Send(message);
            sw.Stop();
            LastRequestElapsed = sw.ElapsedMilliseconds;
            return Newtonsoft.Json.Linq.JObject.Parse(response.Content.ReadAsStringAsync().Result);
        }

        public static string ReadMessages(dynamic response)
        {
            StringBuilder sb = new StringBuilder();

            foreach (dynamic message in response.messages)
            {
                sb.AppendLine($"[{message.level}] {message.message}");
            }

            return sb.ToString();
        }

        internal static void AppendOutput(int padding, object message, params object[] concat)
        {
            string f = message.ToString() + string.Join(' ', concat ?? new string[] { });
            foreach(string l in f.Split('\n'))
            {
                if (l.Trim().Length == 0) continue;
                output.AppendLine(new string(' ', padding * 3) + l.Trim());
                Console.WriteLine(new string(' ', padding * 3) + l.Trim());
            }
        }

        static void Main(string[] args)
        {
            Console.WriteLine("Starless Sky Network Tester");
            Console.WriteLine("Version 1.0.0.0 built for SLS 0.12.335");
            Console.WriteLine();

            if (args.Length != 1)
            {
                Console.WriteLine("Usage: slstester.exe <full-sls-address>");
                Environment.Exit(1);
            }

            Console.ForegroundColor = ConsoleColor.Yellow;
            Console.WriteLine("Always use official repositories when dealing with sensitive information.");
            Console.WriteLine("Official repository: https://github.com/project-principium/starless-sky-network");
            Console.WriteLine();
            Console.ForegroundColor = ConsoleColor.Gray;
            Console.WriteLine($"Trying to listen to {args[0]}. . .");

            Console.WriteLine();
            List<ISkylessSkyTest> Tests = new List<ISkylessSkyTest>();
            Dictionary<string, string> Bag = new Dictionary<string, string>();
            Tests.Add(new PingTest());
            Tests.Add(new KeyPairGenerationTest());
            Tests.Add(new PublicIdentitySetTest());
            Tests.Add(new PublicIdentityGetTest());
            Tests.Add(new PublicIdentityDeleteTest());
            Tests.Add(new MessageSendTest());
            Tests.Add(new MessageFetchTest());
            Tests.Add(new MessageReceiverReadTest());
            Tests.Add(new MessageSenderReadTest());
            Tests.Add(new MessageEditTest());
            Tests.Add(new MessageDeleteReceiver());
            Tests.Add(new MessageSendTest());
            Tests.Add(new MessageDeleteSender());
            Tests.Add(new CreateSignRequestTest());
            Tests.Add(new AcceptSignRequestTest());
            Tests.Add(new ViewSignRequestTest());

            Stopwatch total = new Stopwatch();
            total.Start();
            foreach (ISkylessSkyTest test in Tests)
            {
                AppendOutput(0, new string('-', 50));
                AppendOutput(0, "Starting test " + test.Name + "...");
                bool pass = test.Run(args[0], ref Bag);
                AppendOutput(0, "Test Status: " + (pass ? "PASS" : "FAILED") + $" in {LastRequestElapsed} ms");
                if (pass == false) Environment.Exit(4);
            }
            total.Stop();
            Console.WriteLine($"All {Tests.Count} tests terminated in {total.Elapsed.Minutes}m {total.Elapsed.Seconds}s {total.Elapsed.Milliseconds}ms");
        }
    }
}
