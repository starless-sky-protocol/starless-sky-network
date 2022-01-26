using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class MessageSenderReadTest : ISkylessSkyTest
    {
        public string Name => "MESSAGE_SENDER_READ_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    public_key = bag["public_key_1"],
                    private_key = bag["private_key"]
                };

                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/messages/sender/" + bag["message_id"], data);
                var messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "ID: ", res.response.id);
                Program.AppendOutput(2, "Size: ", res.response.size);

                Program.AppendOutput(2, "Manifest: ");
                Program.AppendOutput(3, "Created at: ", res.response.manifest.created_at);
                Program.AppendOutput(3, "Updated at: ", res.response.manifest.updated_at);
                Program.AppendOutput(3, "Is modified: ", res.response.manifest.is_modified);
                Program.AppendOutput(3, "Blake3 digest: ", res.response.manifest.message_blake3_digest);

                Program.AppendOutput(2, "Pair: ");
                Program.AppendOutput(3, "Sender Public Key: ", res.response.pair.sender_public_key);
                Program.AppendOutput(3, "Receiver Public Key: ", res.response.pair.receiver_public_key);

                Program.AppendOutput(2, "Message: ");
                Program.AppendOutput(3, "Subject: ", res.response.message.subject);
                Program.AppendOutput(3, "Content: ", res.response.message.content);

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
