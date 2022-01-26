using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace StarlessSkyNetworkTester.Tests
{
    public interface ISkylessSkyTest
    {
        public bool Run(string uri, ref Dictionary<string, string> bag);
        public string Name { get; }
    }
}
