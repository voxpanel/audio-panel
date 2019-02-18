package com.shoutcast.stm.radio_nome;

import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Typeface;
import android.graphics.drawable.ColorDrawable;
import android.os.Vibrator;
import android.view.GestureDetector;
import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TableLayout;
import android.widget.TextView;

import java.util.ArrayList;

import com.shoutcast.stm.radio_nome.R;

/**
 * Created by Mark Bús on 2014.06.29..
 */
public class RadioList extends LinearLayout {

    private static ArrayList<RadioListElement> radioList;
    private static MusicPlayer mpt = new MusicPlayer();
    private TableLayout radioListUI;


    public RadioList(Context context, ArrayList<RadioListElement> radioList, TableLayout radioListUI) {
        super(context);
        View.inflate(context, R.layout.fragment_radios, this);
        this.radioList = radioList;
        this.radioListUI = radioListUI;
    }

    public static void resetOldSelectedRadio() {
        for (final RadioListElement rle : radioList) {
            if (rle.isPlayBol())
                rle.setElementDefault();
        }
    }

    public static void nextOrPreviousRadioStation(int action, final TextView mainRadioLocation, final TextView mainRadioName) throws Exception {
        int index = -1;
        for (final RadioListElement rle : radioList) {
            if (rle.isPlayBol())
                index = radioList.indexOf(rle);
        }
        switch (action) {
            case 1:
                index = index + 1;
                if (index < radioList.size()) {
                    resetOldSelectedRadio();
                    radioList.get(index).touchUP();
                    mainRadioLocation.setText(radioList.get(index).getFrequency());
                    mainRadioName.setText(radioList.get(index).getName());
                    mpt.play(radioList.get(index));
                }
                break;
            case -1:
                index = index - 1;
                if (index >= 0) {
                    resetOldSelectedRadio();
                    radioList.get(index).touchUP();
                    mainRadioLocation.setText(radioList.get(index).getName());
                    mainRadioName.setText(radioList.get(index).getFrequency());
                    mpt.play(radioList.get(index));
                }
                break;

            case 0:
                if (index != -1)
                    mpt.play(radioList.get(index));
                else
                    MainActivity.nextPage();
                break;
        }
    }

    public static void listeningReset(final TextView mainRadioLocation) throws Exception {
        int index = -1;
        for (final RadioListElement rle : radioList) {
            if (rle.getName().equals(mainRadioLocation.getText()))
                index = radioList.indexOf(rle);
        }
        radioList.get(index).touchUP();
    }

    public void addRadioStations(final TextView mainRadioLocation, final TextView mainRadioName) {
        for (final RadioListElement rle : radioList) {
            radioListUI.addView(rle);
        }
        try {
            listeningReset(mainRadioLocation);
        } catch (Exception e) {
            e.getMessage();
        }
    }
}

