using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class PublicIdentityDeleteTest : ISkylessSkyTest
    {
        public string Name => "PUBLIC_IDENTITY_DELETE";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key"],
                };

                dynamic res = Program.JSONRequest(HttpMethod.Delete, uri + "/identity", data);
                string messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);

                return true;
            } catch (Exception ex)
            {
                Program.AppendOutput(0, "Exception thrown at " + Name + ": " + ex.Message);
                return false;
            }
        }
    }
}
