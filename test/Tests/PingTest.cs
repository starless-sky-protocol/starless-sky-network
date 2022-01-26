using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class PingTest : ISkylessSkyTest
    {
        public string Name => "PING_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/ping", null);
                string messages = Program.ReadMessages(res);
                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "SLS Server Version: ", res.response.sls_server_version);
                Program.AppendOutput(2, "PHP Server Version: ", res.response.php_version);
                Program.AppendOutput(2, "Operating System: ", res.response.operating_system);
                Program.AppendOutput(2, "Server Information: ");
                Program.AppendOutput(3, "Allow not identified senders: ", res.response.server_info.allow_not_identified_senders);
                Program.AppendOutput(3, "Allow message deletion: ", res.response.server_info.allow_message_deletion);
                Program.AppendOutput(3, "Allow message edit: ", res.response.server_info.allow_message_edit);
                Program.AppendOutput(3, "Message max size: ", res.response.server_info.sign_message_max_size);
                Program.AppendOutput(3, "Sign message max size: ", res.response.server_info.sign_max_expiration + " seconds");
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
