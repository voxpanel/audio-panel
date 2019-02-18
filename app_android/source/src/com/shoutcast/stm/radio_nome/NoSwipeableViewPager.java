package com.shoutcast.stm.radio_nome;

import com.shoutcast.stm.radio_nome.R;

import android.content.Context;
import android.support.v4.view.ViewPager;
import android.util.AttributeSet;
import android.view.MotionEvent;

/**
 * Created by Márk on 2015.02.28..
 */
public class NoSwipeableViewPager extends ViewPager {

    private String key = "";

    public NoSwipeableViewPager(Context context) {
        super(context);
    }

    public NoSwipeableViewPager(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    @Override
    public boolean onTouchEvent(MotionEvent ev) {
        if(MainActivity.page!=2 && MainActivity.pos==0){
        if (ev.getAction() == MotionEvent.ACTION_MOVE) {
            key = "m";
        } else if (ev.getAction() == MotionEvent.ACTION_UP) {
            key = key + "u";
            if (key.equals("u") || key.equals("mu")) {
                key = "";
                MainActivity.play(this.getResources().getString(R.string.radio_location));
            } else key = "";
        }
        }
        /*System.out.println(ev.getAction());
        key=key+""+ev.getAction();
        System.out.println(key);
        if(ev.getAction()==1){
            System.out.println("vege "+key);
            if(key.contains("021") || key.contains("01") ){
                System.out.println("indulhat");
                key="";
            } else key="";
        }*/
        return super.onTouchEvent(ev);
    }

    @Override
    public boolean onInterceptTouchEvent(MotionEvent ev) {
        //System.out.println(ev.getAction());
        return super.onInterceptTouchEvent(ev);
    }
}
