using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    internal class ViewSignRequestTest : ISkylessSkyTest
    {
        public string Name => "VIEW_SIGN_REQUEST_TEST";

        public bool Run(string uri, ref Dictionary<string, string> bag)
        {
#if !DEBUG
            try
            {
#endif
                var data = new
                {
                    private_key = bag["private_key"],
                    public_key = bag["public_key_1"],
                    id = bag["contract_id"]
                };
                
                dynamic res = Program.JSONRequest(HttpMethod.Get, uri + "/sign", data);
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
                Program.AppendOutput(3, "Sign Status: ", res.response.status.sign_status ?? "<null>");
                Program.AppendOutput(3, "Date: ", res.response.status.date ?? "<null>");

                return true;
#if !DEBUG
            }
            catch (Exception ex)
            {
                Program.AppendOutput(0, "Exception thrown at " + Name + ": " + ex.Message);
                return false;
            }
#endif
        }
    }
}
