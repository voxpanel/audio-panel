package com.cover;

import android.os.AsyncTask;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;

/**
 * Created by markO on 2015. 08. 05..
 */
public class UrlGenerator extends AsyncTask<String, Void, Void> {

    private InputStream inputStream;
    private String result;
    private static String imageUrl;
    private String url;

    public static String getImageUrl() {
        return imageUrl;
    }

    @Override
    protected Void doInBackground(String... params) {

        System.out.println("test start");
        url = "https://itunes.apple.com/search?term=" + params[0].replace(" ", "%20") + "&limit=1";
        imageUrl = null;
        int j = 0;

        while(j!=2) {
            ArrayList<NameValuePair> param = new ArrayList<NameValuePair>();
            try {
                HttpClient httpClient = new DefaultHttpClient();
                HttpPost httpPost = new HttpPost(url);
                httpPost.setEntity(new UrlEncodedFormEntity(param));
                HttpResponse httpResponse = httpClient.execute(httpPost);
                HttpEntity httpEntity = httpResponse.getEntity();

                inputStream = httpEntity.getContent();

                BufferedReader bReader = new BufferedReader(new InputStreamReader(inputStream, "utf-8"), 8);
                StringBuilder sBuilder = new StringBuilder();

                String line = null;
                while ((line = bReader.readLine()) != null)
                    sBuilder.append(line + "\n");

                inputStream.close();
                result = sBuilder.toString();

                JSONObject object = new JSONObject(result);
                JSONArray jArray = object.getJSONArray("results");
                for (int i = 0; i < jArray.length(); i++) {
                    JSONObject jObject = jArray.getJSONObject(i);
                    String name = jObject.getString("artworkUrl60").replace("60x60", "300x300");
                    imageUrl = name;
                    break;
                }
                if(imageUrl==null && j!=1){
                    url = "https://itunes.apple.com/search?term=" + featDeleter(params[0]).replace(" ", "%20") + "&limit=1";
                    j++;
                }else
                    return null;
                } catch (Exception e) {
                //System.out.println("test hiba "+e.getMessage());
                imageUrl = null;
                e.getMessage();
                return null;
            }
        }
        //System.out.println("test "+imageUrl);
        return null;
    }

    protected void onPostExecute(Void v) {
        try {
            new ImageDownloader().execute();
        } catch (Exception e) {
            e.getMessage();
        }
    }

    public String getContain(String title) {
        String[] feats = {"featuring","feat.","feat", "Feat", "Feat.", "ft.","ft", "(ft", "(Ft", "Ft.", "Ft", "(Feat", "(Feat.", "(Original", "(original", "(Remix", "(remix", "(Le"};
        for (int i =0; i<feats.length; i++){
            if(title.contains(feats[i]))
                return feats[i];
        }
        return null;
    }

    public String featDeleter(String originalTitle) {
        String finalValue = originalTitle;
        String[] title = originalTitle.split(" - ");
        if (title.length == 2) {
            finalValue = "";
            String[] parts = title[0].split(" ");
            for (int i = 0; i < parts.length; i++) {
                if (!parts[i].equals(getContain(parts[i])))
                    finalValue = finalValue + " " + parts[i];
                else break;
            }
            finalValue = finalValue + " - ";
            parts = title[1].split(" ");
            for (int i = 0; i < parts.length; i++) {
                if (!parts[i].equals(getContain(parts[i])))
                    finalValue = finalValue + " " + parts[i];
                else break;
            }
        }
        return finalValue;
    }
}