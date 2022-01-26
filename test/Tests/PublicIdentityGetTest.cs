using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class PublicIdentityGetTest : ISkylessSkyTest
    {
        public string Name => "PUBLIC_IDENTITY_GET";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    public_key = bag["public_key"]
                };

                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/identity", data);
                string messages = Program.ReadMessages(res);

                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "Name: ", res.response.name);
                Program.AppendOutput(2, "Biography: ", res.response.biography);

                return true;
            } catch (Exception ex)
            {
                Program.AppendOutput(0, "Exception thrown at " + Name + ": " + ex.Message);
                return false;
            }
        }
    }
}
