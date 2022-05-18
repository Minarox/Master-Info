package com.google.ar.core.examples.java.augmentedimage;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.widget.Button;

import java.io.FileNotFoundException;

public class ShareFormActivity extends AppCompatActivity {

    // Device model
    String PhoneModel = android.os.Build.MODEL;

    // Android version
    String AndroidVersion = android.os.Build.VERSION.RELEASE;

    private String ImgString;
    private String path;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_share_form);
        Button ShareButton = (Button) findViewById(R.id.ShareButton);

        Intent intent = getIntent();
        ImgString = intent.getStringExtra("Name");

        ShareButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                try {
                    path = MediaStore.Images.Media.insertImage(getContentResolver(), ImgString, "", null);
                } catch (FileNotFoundException e) {
                    e.printStackTrace();
                }

                Uri pictureUri = Uri.parse(path);

                Intent myIntent = new Intent(Intent.ACTION_SEND);
                myIntent.setType("image/png");
                myIntent.putExtra(Intent.EXTRA_STREAM, pictureUri);
                myIntent.putExtra(Intent.EXTRA_TEXT, "Your photo");

                startActivity(Intent.createChooser(myIntent, "Share your picture"));
            }
        });
    }
}