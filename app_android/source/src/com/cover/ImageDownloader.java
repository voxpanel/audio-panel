package com.cover;

import android.annotation.SuppressLint;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.AsyncTask;

import com.shoutcast.stm.radio_nome.MainScreen;
import com.shoutcast.stm.radio_nome.R;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

import java.io.InputStream;

/**
 * Created by markO on 2015. 08. 05..
 */
@SuppressLint("ResourceAsColor") public class ImageDownloader extends AsyncTask<String,Void,Void> {
    private Bitmap image=null;
    private int color=0;

    @Override
    protected Void doInBackground(String... params) {
        // TODO Auto-generated method stub
        try {
            if(UrlGenerator.getImageUrl()!=null){
                color = downloadBitmap(UrlGenerator.getImageUrl().replace("300x300", "1x1")).getPixel(0, 0);
                image = downloadBitmap(UrlGenerator.getImageUrl());
            }
        }
        catch(Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        // TODO Auto-generated method stub
        super.onPostExecute(result);
        if(image!=null){
            CoverGenerator.setCover(image);
            if(Color.red(color) >= 127.5 || Color.green(color) >= 127.5 || Color.blue(color) >= 127.5)
                MainScreen.setRadioListNameColor(Color.BLACK);
            else MainScreen.setRadioListNameColor(Color.WHITE);
            }
        else{
            CoverGenerator.setCover(R.drawable.radio_logo);
            MainScreen.setRadioListNameColor(R.color.track_title_start);
        }
    }

    private Bitmap downloadBitmap(String url) {
        //System.out.println("image url "+url);
        DefaultHttpClient client = new DefaultHttpClient();
        HttpGet getRequest = new HttpGet(url);
        try {
            HttpResponse response = client.execute(getRequest);
            final int statusCode = response.getStatusLine().getStatusCode();

            if (statusCode != HttpStatus.SC_OK)
                return null;

            final HttpEntity entity = response.getEntity();
            if (entity != null) {
                InputStream inputStream = null;
                try {
                    inputStream = entity.getContent();
                    image = BitmapFactory.decodeStream(inputStream);
                } finally {
                    if (inputStream != null)
                        inputStream.close();
                    entity.consumeContent();
                }
            }
        } catch (Exception e) {
            getRequest.abort();
            e.getMessage();
        }
        return image;
    }
}
