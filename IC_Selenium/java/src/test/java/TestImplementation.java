import org.junit.*;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;

import static org.hamcrest.CoreMatchers.containsString;
import static org.hamcrest.MatcherAssert.assertThat;

import java.net.MalformedURLException;
import java.net.URL;


public class TestImplementation {

    private static WebDriver driver;

    @BeforeClass
    public static void setupWebdriverChromeDriver() throws MalformedURLException {

      //String Hub = "http://localhost:4444";
      //DesiredCapabilities caps = new DesiredCapabilities();
      //caps.setBrowserName("chrome");
      //driver = new RemoteWebDriver(new URL(Hub), caps);

       System.setProperty("webdriver.chrome.driver", "C:\\Users\\FRFTPMEA\\OneDrive - Alfa Laval\\Desktop\\EPSI\\TP_IntegrationContinue\\Selenium\\selenium\\java\\src\\test\\resources\\chromedriver\\chromedriver.exe");
        // for Firefox 
	// System.setProperty("webdriver.gecko.driver", System.getProperty("user.dir") + "/src/test/resources/geckodriver");
    }

    

    @Before
    public void setup() {
        driver = new ChromeDriver();
        // for Firefox
	// driver = new FirefoxDriver();
    }

    @After
    public void teardown() {
        if (driver != null) {
            driver.quit();
        }
    }

    @Test
    public void verifyGitHubTitle() {
        //driver.get("https://www.github.com");
        //assertThat(driver.getTitle(), containsString("GitHub"));
        System.out.println("Test #1");
        
    }
}
