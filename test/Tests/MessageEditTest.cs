using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class MessageEditTest : ISkylessSkyTest
    {
        public string Name => "MESSAGE_EDIT_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key"],
                    public_key = bag["public_key_1"],
                    message = new
                    {
                        content = "New message",
                        subject = "Open this"
                    }
                };

                dynamic res = Program.JSONRequest(HttpMethod.Put, uri + "/messages/" + bag["message_id"], data);
                var messages = Program.ReadMessages(res);

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
