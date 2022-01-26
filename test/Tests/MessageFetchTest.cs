using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class MessageFetchTest : ISkylessSkyTest
    {
        public string Name => "MESSAGE_FETCH_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key_1"],
                    pagination_data = new {
                        skip = 0,
                        take = -1
                    }
                };

                var res = Program.JSONRequest(HttpMethod.Get, uri + "/messages", data);
                dynamic messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "Pagination Data: ");
                Program.AppendOutput(3, "Total: ", res.response.pagination_data.total);
                Program.AppendOutput(3, "Query: ", res.response.pagination_data.query);
                Program.AppendOutput(2, "Message count: ", ((Newtonsoft.Json.Linq.JArray)res.response.messages).Count);

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
