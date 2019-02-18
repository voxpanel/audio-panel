package com.shoutcast.stm.radio_nome;

import android.content.Context;
import android.widget.TableLayout;
import android.widget.TextView;

import java.io.BufferedReader;
import java.io.FileOutputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;

import com.shoutcast.stm.radio_nome.R;

/**
 * Created by Mark on 2014.08.06..
 */
public class DataManager {

    private Context context;

    public DataManager(Context context) {
        this.context = context;
    }

    public void createRadioListForRadioScreen(TableLayout UIRadioList, ArrayList<String> userRadios, TextView radioListName, TextView radioListLocation) {
        ArrayList<RadioListElement> radioArray = new ArrayList<RadioListElement>();
        radioArray.add(new RadioListElement(context, context.getResources().getString(R.string.radio_name), context.getResources().getString(R.string.radio_location), context.getResources().getString(R.string.radio_url)));
        UIRadioList.removeAllViews();
        RadioList radioList = new RadioList(context, radioArray, UIRadioList);
        radioList.addRadioStations(radioListName, radioListLocation);

    }

}





















