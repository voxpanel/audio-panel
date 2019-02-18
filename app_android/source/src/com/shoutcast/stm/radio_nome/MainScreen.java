package com.shoutcast.stm.radio_nome;

import com.shoutcast.stm.radio_nome.R;

import android.content.Intent;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

/**
 * Created by Márk on 2014.02.23..
 */
public class MainScreen extends Fragment {
    private static ImageView loading;
    private LinearLayout share;
    private static TextView radioListLocation;
    protected static FrameLayout mainView;

    public static TextView getRadioListName() {
        return radioListName;
    }

    public static void setRadioListNameColor(int c) {
        radioListName.setTextColor(c);
    }

    //ez
    private static TextView radioListName;


    public static ImageView getLoadingImage() {
        return loading;
    }

    public static void setRadioListName(String test) {
        radioListName.setText(test);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View android = inflater.inflate(R.layout.main_main, container, false);
        mainView = (FrameLayout) android.findViewById(R.id.mainView);
        mainView.setBackgroundDrawable(MainActivity.ob);
        loading = (ImageView) android.findViewById(R.id.loading);
        radioListLocation = (TextView) android.findViewById(R.id.mainRadioName);
        radioListName = (TextView) android.findViewById(R.id.mainRadioLocation);
        Typeface fontRegular = Typeface.createFromAsset(this.getActivity().getAssets(), "fonts/font.otf");
        radioListLocation.setTypeface(fontRegular);
        radioListName.setTypeface(fontRegular);
        share = (LinearLayout) android.findViewById(R.id.shareImage);
        share.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(Intent.ACTION_SEND);
                intent.setType("text/plain");
                intent.putExtra(Intent.EXTRA_TEXT, getString(R.string.share_url));
                startActivity(Intent.createChooser(intent, "Share with"));
            }
        });
        if (MusicPlayer.isStarted()) {
            radioListLocation.setText(getString(R.string.radio_name));
        }
        return android;
    }
}
