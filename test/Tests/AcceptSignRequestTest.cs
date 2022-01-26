using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class AcceptSignRequestTest : ISkylessSkyTest
    {
        public string Name => "ACCEPT_SIGN_REQUEST_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
            try
            {
                var data = new
                {
                    private_key = bag["private_key_1"],
                    id = bag["contract_id"]
                };
                
                dynamic res = Program.JSONRequest(HttpMethod.Post, uri + "/sign/sign", data);
                string messages = Program.ReadMessages(res);
                Program.AppendOutput(1, "Request success: ", res.success);
                Program.AppendOutput(1, "Messages: ");
                Program.AppendOutput(2, messages.Length == 0 ? "<none>" : messages);
                Program.AppendOutput(1, "Response: ");
                Program.AppendOutput(2, "ID: ", res.response.id);
                Program.AppendOutput(2, "Issued: ", res.response.issued);
                Program.AppendOutput(2, "Expires: ", res.response.expires);
                Program.AppendOutput(2, "Message: ", res.response.message);
                Program.AppendOutput(2, "Issuer: ");
                Program.AppendOutput(3, "Public Key: ", res.response.issuer.public_key);
                Program.AppendOutput(2, "Signer: ");
                Program.AppendOutput(3, "Public Key: ", res.response.signer.public_key);
                Program.AppendOutput(2, "Status: ");
                Program.AppendOutput(3, "Sign Status: ", res.response.status.sign_status);
                Program.AppendOutput(3, "Date: ", res.response.status.date);

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
