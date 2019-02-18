package com.shoutcast.stm.radio_nome;

import android.content.Context;
import android.media.AudioTrack;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Build;
import android.os.Handler;

import com.google.android.exoplayer2.DefaultLoadControl;
import com.google.android.exoplayer2.ExoPlaybackException;
import com.google.android.exoplayer2.ExoPlayer;
import com.google.android.exoplayer2.ExoPlayerFactory;
import com.google.android.exoplayer2.SimpleExoPlayer;
import com.google.android.exoplayer2.Timeline;
import com.google.android.exoplayer2.extractor.DefaultExtractorsFactory;
import com.google.android.exoplayer2.extractor.ExtractorsFactory;
import com.google.android.exoplayer2.source.ExtractorMediaSource;
import com.google.android.exoplayer2.source.MediaSource;
import com.google.android.exoplayer2.trackselection.AdaptiveVideoTrackSelection;
import com.google.android.exoplayer2.trackselection.DefaultTrackSelector;
import com.google.android.exoplayer2.trackselection.TrackSelector;
import com.google.android.exoplayer2.upstream.DataSource;
import com.google.android.exoplayer2.upstream.DefaultBandwidthMeter;
import com.google.android.exoplayer2.upstream.DefaultDataSourceFactory;
import com.google.android.exoplayer2.util.Util;

import java.net.URL;
import java.util.Timer;
import java.util.TimerTask;

import com.shoutcast.stm.radio_nome.R;
import com.spoledge.aacdecoder.MultiPlayer;
import com.spoledge.aacdecoder.PlayerCallback;

/**
 * Created by User on 2014.07.03..
 */
public class MusicPlayer {
    private static boolean isStarted = false;
    private static String trackTitle = "";
    private static String radioName = "";
    private Context context;
    private static SimpleExoPlayer player;
    private ConnectivityManager cm;
    private NetworkInfo netInfo;
    private RadioListElement radioListElement;
    private Timer timer = new Timer();
    private boolean timerIndicator = false;

    private static MultiPlayer multiPlayer;

    public static boolean isWorking() {
        return isWorking;
    }

    public static void setIsWorking(boolean isWorking) {
        MusicPlayer.isWorking = isWorking;
    }

    private static boolean isWorking = true;

    public static String getRadioName() {
        return radioName;
    }

    public static String getTrackTitle() {
        return trackTitle;
    }

    public static boolean isStarted() {
        return isStarted;
    }

    public static void stopMediaPlayer() {
        isStarted = false;
        player.stop();
        multiPlayer.stop();
    }

    public void play(RadioListElement rle) {
        startThread();
        isWorking = true;
        isStarted = true;
        radioListElement = rle;
        context = radioListElement.getContext();
        MainActivity.setViewPagerSwitch();
        DefaultBandwidthMeter bandwidthMeter = new DefaultBandwidthMeter();
        TrackSelector trackSelector = new DefaultTrackSelector(new Handler(), new AdaptiveVideoTrackSelection.Factory(bandwidthMeter));
        player = ExoPlayerFactory.newSimpleInstance(context, trackSelector, new DefaultLoadControl(), context.getString(R.string.item_purchase_code));
        DataSource.Factory dataSourceFactory = new DefaultDataSourceFactory(context, Util.getUserAgent(context, "streamradio"), bandwidthMeter);
        ExtractorsFactory extractorsFactory = new DefaultExtractorsFactory();
        MediaSource audioSource = new ExtractorMediaSource(Uri.parse(radioListElement.getUrl()), dataSourceFactory, extractorsFactory, null, null);
        player.prepare(audioSource);
        player.setPlayWhenReady(true);
        player.addListener(new ExoPlayer.EventListener() {
            @Override
            public void onLoadingChanged(boolean isLoading) {
                if(isLoading){
                    MainActivity.startBufferingAnimation();
                }
            }

            @Override
            public void onPlayerStateChanged(boolean playWhenReady, int playbackState) {
                if(playbackState==3){
                    MainActivity.stopBufferingAnimation();
                    isStarted = true;
                }else{
                    isStarted = false;
                }
            }

            @Override
            public void onTimelineChanged(Timeline timeline, Object manifest) {

            }

            @Override
            public void onPlayerError(ExoPlaybackException error) {
                if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION.SDK_INT) {
//                if (android.os.Build.VERSION.SDK_INT < Build.VERSION_CODES.LOLLIPOP) {
                    try {
                        try {
                            multiPlayer.stop();
                        } catch (Exception e) {
                            e.getMessage();
                        }
                        MainActivity.newNotification(MusicPlayer.getRadioName(), true);
                        multiPlayer = new MultiPlayer(new PlayerCallback() {

                            @Override
                            public void playerStopped(int arg0) {
                                isStarted = false;
                            }

                            @Override
                            public void playerStarted() {
                                isStarted = true;
                                try {
                                    MainActivity.stopBufferingAnimation();
                                } catch (Exception e) {
                                    e.getMessage();
                                }
                            }

                            @Override
                            public void playerPCMFeedBuffer(boolean arg0, int arg1, int arg2) {
                                // TODO Auto-generated method stub

                            }

                            @Override
                            public void playerMetadata(String arg0, String arg1) {
                                // TODO Auto-generated method stub
                            }

                            @Override
                            public void playerException(Throwable arg0) {
                                // TODO Auto-generated method stub
                                isWorking = false;
                                try {
                                    cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
                                    netInfo = cm.getActiveNetworkInfo();
                                    if (netInfo != null && netInfo.isConnectedOrConnecting()) {
                                        MainActivity.stopBufferingAnimation();
                                        isWorking = false;

                                    } else {
                                        MainActivity.stopBufferingAnimation();
                                        isWorking = false;
                                    }
                                } catch (Exception e) {
                                    // TODO: handle exception
                                }

                            }

                            @Override
                            public void playerAudioTrackCreated(AudioTrack arg0) {
                                // TODO Auto-generated method stub

                            }
                        }, 750, 700);
                        multiPlayer.playAsync(radioListElement.getUrl().toString());

                        try {
                            java.net.URL.setURLStreamHandlerFactory(new java.net.URLStreamHandlerFactory() {
                                public java.net.URLStreamHandler createURLStreamHandler(String protocol) {
                                    if ("icy".equals(protocol))
                                        return new com.spoledge.aacdecoder.IcyURLStreamHandler();
                                    return new com.spoledge.aacdecoder.IcyURLStreamHandler();
                                }
                            });
                        } catch (Exception e) {
                            e.printStackTrace();
                        }

                    } catch (Exception e) {
                        isWorking = false;
                        try {
                            cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
                            netInfo = cm.getActiveNetworkInfo();
                            if (netInfo != null && netInfo.isConnectedOrConnecting()) {
                                MainActivity.stopBufferingAnimation();
                                isWorking = false;

                            } else {
                                MainActivity.stopBufferingAnimation();
                                isWorking = false;
                            }
                        } catch (Exception e2) {
                            // TODO: handle exception
                        }
                    }
                } else {
                    isWorking = false;
                    try {
                        cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
                        netInfo = cm.getActiveNetworkInfo();
                        if (netInfo != null && netInfo.isConnectedOrConnecting()) {
                            MainActivity.stopBufferingAnimation();
                            isWorking = false;

                        } else {
                            MainActivity.stopBufferingAnimation();
                            isWorking = false;
                        }
                    } catch (Exception e2) {
                        // TODO: handle exception
                    }
                }
            }

            @Override
            public void onPositionDiscontinuity() {

            }
        });
        radioListElement.getName();
        radioName = radioListElement.getName();
    }
    public void startThread() {
        if (!timerIndicator) {
            timerIndicator = true;
            timer.schedule(new TimerTask() {
                public void run() {
                    if (isStarted) {
                        URL url;
                        try {
                            url = new URL(radioListElement.getUrl());
                            IcyStreamMeta icy = new IcyStreamMeta(url);
                            if (icy.getArtist().length() > 0 && icy.getTitle().length() > 0) {
                                String title = icy.getArtist() + " - " + icy.getTitle();
                                trackTitle = new String(title.getBytes("ISO-8859-1"), "UTF-8");
                            } else {
                                String title = icy.getArtist() + "" + icy.getTitle();
                                trackTitle = new String(title.getBytes("ISO-8859-1"), "UTF-8");
                            }
                        } catch (Exception e) {
                            // TODO Auto-generated catch block
                            e.printStackTrace();
                        }
                    }
                }
            }, 0, 1000);
        }
    }
}
