# Building Tests

Tests are relevant to test the complete functioning of the Starless Sky Network and identify problems with it. In this repository, we have a testing utility that tests all the public functionality of an SLS network. Always test locally when key generation is private on your network.

To start testing, you will need to have a working Starless Sky environment on your machine. To do this, see the section `getting-started.md`. Also, you'll need **.NET 5 or 6**. You can download it at Microsoft [.NET download page](https://dotnet.microsoft.com/en-us/download/dotnet). Remember to download the SDK version for your operating system if you want to build the tests.

After installing .NET 5, check your version with:

    dotnet --version
    > 6.0.100

With this repository cloned, navigate to the "test" folder and run the command "dotnet build":

    cd test
    dotnet build

After building your application, a path to it will be exposed in the terminal. Run the application following the pattern:

    cd bin/Debug/net5.0
    dotnet StarlessSkyNetworkTester.dll http://localhost

Replace `http://localhost` with the address where the network server is running.

> Note: do not terminate the URL with `/`, as the testing utility automatically inserts this character.