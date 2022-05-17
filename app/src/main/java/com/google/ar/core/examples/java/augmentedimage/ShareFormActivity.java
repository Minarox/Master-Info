package com.google.ar.core.examples.java.augmentedimage;

import androidx.appcompat.app.AppCompatActivity;

import android.os.Bundle;

public class ShareFormActivity extends AppCompatActivity {

    // Device model
    String PhoneModel = android.os.Build.MODEL;

    // Android version
    String AndroidVersion = android.os.Build.VERSION.RELEASE;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_share_form);
    }
}