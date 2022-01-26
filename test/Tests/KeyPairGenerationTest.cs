using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class KeyPairGenerationTest : ISkylessSkyTest
    {
        public string Name => "KEYPAIR_GENERATION_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/identity/generate-keypair", null);
                string messages = Program.ReadMessages(res);
                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "Private Key length: ", Convert.ToString(res.response.private_key).Length);
                Program.AppendOutput(2, "Public Key: ", res.response.public_key);

                bag.Add("private_key", res.response.private_key.ToString());
                bag.Add("public_key", res.response.public_key.ToString());

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
