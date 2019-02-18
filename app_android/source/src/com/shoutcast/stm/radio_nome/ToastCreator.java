package com.shoutcast.stm.radio_nome;

import android.content.Context;
import android.view.Gravity;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

/**
 * Created by User on 2014.07.11..
 */
public class ToastCreator {
    private Context context;

    public ToastCreator(Context context) {
        this.context = context;
    }

    public void show(int imageResId, String text) {
        Toast toast = Toast.makeText(context, text, Toast.LENGTH_SHORT);
        toast.setGravity(Gravity.CENTER, 0, 0);
        LinearLayout toastView = (LinearLayout) toast.getView();
        ImageView iw = new ImageView(context);
        iw.setImageResource(imageResId);
        toastView.addView(iw, 0);
        toast.show();
    }


}
