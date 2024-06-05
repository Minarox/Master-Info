package com.example.mspr_arcore;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import android.Manifest;
import android.app.Activity;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.os.Handler;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.Toast;

import com.google.ar.core.ArCoreApk;
import com.google.ar.core.CameraConfig;
import com.google.ar.core.CameraConfigFilter;
import com.google.ar.core.Config;
import com.google.ar.core.Session;
import com.google.ar.core.exceptions.UnavailableApkTooOldException;
import com.google.ar.core.exceptions.UnavailableArcoreNotInstalledException;
import com.google.ar.core.exceptions.UnavailableDeviceNotCompatibleException;
import com.google.ar.core.exceptions.UnavailableException;
import com.google.ar.core.exceptions.UnavailableSdkTooOldException;
import com.google.ar.core.exceptions.UnavailableUserDeclinedInstallationException;

import java.util.EnumSet;
import java.util.List;

public class MainActivity extends AppCompatActivity {

    private static final String TAG = "oui";
    Button mArButton;

    Session session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_main);
        mArButton = findViewById(R.id.mArButton);
        //AffichageImg = findViewById(R.id.AffichageImgId );

        // Check if ar core is supported and up to date then create a session
        if (isARCoreSupportedAndUpToDate()) {
            createSession();
            Log.i("CreateSession ", "reussis");
        } else {
            Log.i("CreateSession ", "non réussis");
        }

        configCam();

    }

    @Override
    protected void onDestroy() {
        if (session != null) {
            // Explicitly close ARCore Session to release native resources.
            // Review the API reference for important considerations before calling close() in apps with
            // more complicated lifecycle requirements:
            // https://developers.google.com/ar/reference/java/arcore/reference/com/google/ar/core/Session#close()
            session.close();
            session = null;
        }

        super.onDestroy();
    }

    // requestInstall(Activity, true) will triggers installation of
    // Google Play Services for AR if necessary.
    private boolean mUserRequestedInstall = true;


    @Override
    protected void onResume() {
        super.onResume();

        // ARCore requires camera permission to operate.
        if (!CameraPermissionHelper.hasCameraPermission(this)) {
            CameraPermissionHelper.requestCameraPermission(this);
            return;
        }

        // Ensure that Google Play Services for AR and ARCore device profile data are
        // installed and up to date.
        try {
            if (session == null) {
                switch (ArCoreApk.getInstance().requestInstall(this, mUserRequestedInstall)) {
                    case INSTALLED:
                        // Success: Safe to create the AR session.
                        session = new Session(this);
                        break;
                    case INSTALL_REQUESTED:
                        // When this method returns `INSTALL_REQUESTED`:
                        // 1. ARCore pauses this activity.
                        // 2. ARCore prompts the user to install or update Google Play
                        //    Services for AR (market://details?id=com.google.ar.core).
                        // 3. ARCore downloads the latest device profile data.
                        // 4. ARCore resumes this activity. The next invocation of
                        //    requestInstall() will either return `INSTALLED` or throw an
                        //    exception if the installation or update did not succeed.
                        mUserRequestedInstall = false;
                }
            }
        } catch (UnavailableUserDeclinedInstallationException e) {
            // Display an appropriate message to the user and return gracefully.
            Toast.makeText(this, "TODO: handle exception " + e, Toast.LENGTH_LONG)
                    .show();
    } catch (UnavailableDeviceNotCompatibleException e) {
            e.printStackTrace();
        } catch (UnavailableSdkTooOldException e) {
            e.printStackTrace();
        } catch (UnavailableArcoreNotInstalledException e) {
            e.printStackTrace();
        } catch (UnavailableApkTooOldException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] results) {
        super.onRequestPermissionsResult(requestCode, permissions, results);
        if (!CameraPermissionHelper.hasCameraPermission(this)) {
            Toast.makeText(this, "Camera permission is needed to run this application", Toast.LENGTH_LONG)
                    .show();
            if (!CameraPermissionHelper.shouldShowRequestPermissionRationale(this)) {
                // Permission denied with checking "Do not ask again".
                CameraPermissionHelper.launchPermissionSettings(this);
            }
            finish();
        }
    }

    public void createSession() {
        // Create a new ARCore session.
        try {
            session = new Session(this);
        } catch (UnavailableDeviceNotCompatibleException e) {
            e.printStackTrace();
        } catch (UnavailableSdkTooOldException e) {
            e.printStackTrace();
        } catch (UnavailableArcoreNotInstalledException e) {
            e.printStackTrace();
        } catch (UnavailableApkTooOldException e) {
            e.printStackTrace();
        }

        // Create a session config.
        Config config = new Config(session);

        // Do feature-specific operations here, such as enabling depth or turning on
        // support for Augmented Faces.

        // Configure the session.
        session.configure(config);
    }

    // Verify that ARCore is installed and using the current version.
    private boolean isARCoreSupportedAndUpToDate() {
        ArCoreApk.Availability availability = ArCoreApk.getInstance().checkAvailability(this);
        switch (availability) {
            case SUPPORTED_INSTALLED:
                return true;

            case SUPPORTED_APK_TOO_OLD:
            case SUPPORTED_NOT_INSTALLED:
                try {
                    // Request ARCore installation or update if needed.
                    ArCoreApk.InstallStatus installStatus = ArCoreApk.getInstance().requestInstall(this, true);
                    switch (installStatus) {
                        case INSTALL_REQUESTED:
                            Log.i(TAG, "ARCore installation requested.");
                            return false;
                        case INSTALLED:
                            return true;
                    }
                } catch (UnavailableException e) {
                    Log.e(TAG, "ARCore not installed", e);
                }
                return false;

            case UNSUPPORTED_DEVICE_NOT_CAPABLE:
                // This device is not supported for AR.
                return false;

            case UNKNOWN_CHECKING:
                // ARCore is checking the availability with a remote query.
                // This function should be called again after waiting 200 ms to determine the query result.
            case UNKNOWN_ERROR:
            case UNKNOWN_TIMED_OUT:
                // There was an error checking for AR availability. This may be due to the device being offline.
                // Handle the error appropriately.
        }
        return false;
    }

    private void configCam() {
        // Create a camera config filter for the session.
        CameraConfigFilter filter = new CameraConfigFilter(session);

        // Return only camera configs that target 30 fps camera capture frame rate.
        filter.setTargetFps(EnumSet.of(CameraConfig.TargetFps.TARGET_FPS_30));

        // Return only camera configs that will not use the depth sensor.
        filter.setDepthSensorUsage(EnumSet.of(CameraConfig.DepthSensorUsage.DO_NOT_USE));

        // Get list of configs that match filter settings.
        // In this case, this list is guaranteed to contain at least one element,
        // because both TargetFps.TARGET_FPS_30 and DepthSensorUsage.DO_NOT_USE
        // are supported on all ARCore supported devices.
        List<CameraConfig> cameraConfigList = session.getSupportedCameraConfigs(filter);

        // Use element 0 from the list of returned camera configs. This is because
        // it contains the camera config that best matches the specified filter
        // settings.
        session.setCameraConfig(cameraConfigList.get(0));
    }
}