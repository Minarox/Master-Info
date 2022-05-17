package com.google.ar.core.examples.java.augmentedimage;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.os.Environment;
import android.util.Log;
import android.widget.ImageView;

import java.io.File;

public class ShareActivity extends AppCompatActivity {

    private String ImgString;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_share);
        ImageView ShowView = findViewById(R.id.ShowImg);

        Intent intent = getIntent();
        ImgString = intent.getStringExtra("Name");

        File imgFile = new File(Environment.getExternalStorageDirectory(), "Pictures/Ar_Core/" + ImgString);

        Bitmap myBitmap = BitmapFactory.decodeFile(imgFile.getAbsolutePath());

        ShowView.setImageBitmap(myBitmap);
    }

    private void ChangeActivity(String ImgName) {
        Intent myIntent = new Intent(this, AugmentedImageActivity.class);
        startActivity(myIntent);
    }

}