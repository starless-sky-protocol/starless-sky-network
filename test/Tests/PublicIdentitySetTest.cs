using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class PublicIdentitySetTest : ISkylessSkyTest
    {
        public string Name => "PUBLIC_IDENTITY_SET";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key"],
                    @public = new {
                        name = "Test public account",
                        biography = "Test public account biography"
                    }
                };

                dynamic res = Program.JSONRequest(HttpMethod.Post, uri + "/identity", data);
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
