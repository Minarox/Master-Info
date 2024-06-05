package com.google.ar.core.examples.java.augmentedimage;

import androidx.appcompat.app.AppCompatActivity;

import android.app.ProgressDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.nio.charset.StandardCharsets;

public class ShareFormActivity extends AppCompatActivity {

    // Device model
    String PhoneModel = android.os.Build.MODEL;

    // Android version
    String AndroidVersion = "Android " + android.os.Build.VERSION.RELEASE;

    private String ImgString;
    private String path;

    private String email;
    private String firstName;
    private String lastName;

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

                EditText editText = (EditText) findViewById(R.id.editTextFirstName);
                firstName = editText.getText().toString();

                EditText editText2 = (EditText) findViewById(R.id.editTextLastName);
                lastName = editText2.getText().toString();

                EditText editText3 = (EditText) findViewById(R.id.editTextEmail);
                email = editText3.getText().toString();

                Log.i("Share", "onClick: Start");
                new SendData().execute();
                Log.i("Share", "onClick: sendata - passe");

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

    private static HttpURLConnection con;
    private static String token;

    private class SendData extends AsyncTask<String, Integer, Long> {

        @Override
        protected Long doInBackground(String... strings) {
            Log.i("SendData", "doInBackground: Starts");
            String response = null;
            try {
                Log.i("SendData", "doInBackground: request 1");
                response = request("https://mspr.minarox.fr/api/login", "grant_type=password&email=app@cerealis.fr&password=test!123", "");
            } catch (IOException e) {
                e.printStackTrace();
            }
            String[] parts = response.split("\"");
            try {
                Log.i("SendData", "doInBackground: request 1");
                Log.i("SendData", "doInBackground: " + firstName);
                response = request("https://mspr.minarox.fr/api/users", "email="+email+"&first_name="+firstName+"&last_name="+lastName+"&device="+AndroidVersion+"", parts[3]);
            } catch (IOException e) {
                e.printStackTrace();
            }
            System.out.println(response);
            return null;
        }
    }

    public static String request(String url, String parameters, String token) throws IOException {
        byte[] postData = parameters.getBytes(StandardCharsets.UTF_8);

        String response;
        try {
            URL myurl = new URL(url);
            con = (HttpURLConnection) myurl.openConnection();
            con.setDoOutput(true);
            con.setRequestMethod("POST");
            con.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
            if (!token.isEmpty()) {
                con.setRequestProperty ("Authorization", "Bearer " + token);
            }

            try (DataOutputStream wr = new DataOutputStream(con.getOutputStream())) {
                wr.write(postData);
            }

            StringBuilder content;
            try (BufferedReader br = new BufferedReader(new InputStreamReader(con.getInputStream()))) {
                String line;
                content = new StringBuilder();
                while ((line = br.readLine()) != null) {
                    content.append(line);
                    content.append(System.lineSeparator());
                }
            }
            response = content.toString();
        } finally {
            con.disconnect();
        }
        return response;
    }
}