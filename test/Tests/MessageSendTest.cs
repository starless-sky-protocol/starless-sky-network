using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class MessageSendTest : ISkylessSkyTest
    {
        public string Name => "MESSAGE_SEND_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/identity/generate-keypair", null);
                string messages = Program.ReadMessages(res);

                if (!bag.ContainsKey("private_key_1"))
                {
                    bag.Add("private_key_1", res.response.private_key.ToString());
                    bag.Add("public_key_1", res.response.public_key.ToString());
                }
                else
                {
                    bag["private_key_1"] = res.response.private_key.ToString();
                    bag["public_key_1"] = res.response.public_key.ToString();
                }

                var data = new
                {
                    private_key = bag["private_key"],
                    public_key = bag["public_key_1"],
                    message = new
                    {
                        content = "Hello, world!",
                        subject = "Open this"
                    }
                };

                res = Program.JSONRequest(HttpMethod.Post, uri + "/messages", data);
                messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "Pair: ");
                Program.AppendOutput(3, "Sender Public Key: ", res.response.pair.sender_public_key);
                Program.AppendOutput(3, "Receiver Public Key: ", res.response.pair.receiver_public_key);
                Program.AppendOutput(2, "Message Length: ", res.response.message_length);
                Program.AppendOutput(2, "ID: ", res.response.id);
                Program.AppendOutput(2, "Blake3 digest: ", res.response.message_blake3_digest);

                if(!bag.ContainsKey("message_id"))
                {
                    bag.Add("message_id", res.response.id.ToString());
                } else
                {
                    bag["message_id"] = res.response.id.ToString();
                }

                return true;
            }
            catch (Exception ex)
            {
                Program.AppendOutput(0, "Exception thrown at " + Name + ": " + ex.Message);
                return false;
            }
        }
    }
}
