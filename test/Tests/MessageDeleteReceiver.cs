using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class MessageDeleteReceiver : ISkylessSkyTest
    {
        public string Name => "MESSAGE_DELETE_FROM_RECEIVER";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key_1"]
                };

                dynamic res = Program.JSONRequest(HttpMethod.Delete, uri + "/messages/receiver/" + bag["message_id"], data);
                var messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);

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
