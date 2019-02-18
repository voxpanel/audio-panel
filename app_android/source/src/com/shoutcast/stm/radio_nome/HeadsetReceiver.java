package com.shoutcast.stm.radio_nome;

import com.shoutcast.stm.radio_nome.R;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

/**
 * Created by User on 2014.07.10..
 */
public class HeadsetReceiver extends BroadcastReceiver {
    private Context context;
    private ToastCreator toastCreator;
    private boolean notRunWhenStart = true;

    public HeadsetReceiver(Context context) {
        this.context = context;
        this.toastCreator = new ToastCreator(context);
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if (notRunWhenStart)
            notRunWhenStart = false;
        else {
            if (intent.getAction().equals(Intent.ACTION_HEADSET_PLUG)) {
                int state = intent.getIntExtra("state", -1);
                switch (state) {
                    case 0:
                        toastCreator.show(R.drawable.volume_muted_light, "Headset unplugged");
                        try {

                            MusicPlayer.stopMediaPlayer();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                        break;
                    case 1:
                        toastCreator.show(R.drawable.headphones, "Headset plugged");
                        try {
                            if(MusicPlayer.isWorking()){
                                RadioList.nextOrPreviousRadioStation(1, MainActivity.getRadioListLocation(), MainActivity.getRadioListName());
                                RadioList.nextOrPreviousRadioStation(0, MainActivity.getRadioListLocation(), MainActivity.getRadioListName());
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                        break;
                    default:
                }
            }
        }
    }
}
