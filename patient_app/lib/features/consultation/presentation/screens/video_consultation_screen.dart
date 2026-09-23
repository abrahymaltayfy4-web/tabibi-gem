import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/glass_card.dart';
import '../cubit/consultation_cubit.dart';
import '../cubit/consultation_state.dart';

class VideoConsultationScreen extends StatelessWidget {
  final int consultationId;

  const VideoConsultationScreen({super.key, required this.consultationId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.lightBackground,
      appBar: AppBar(
        title: const Text('الاستشارة الطبية المباشرة'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: BlocConsumer<ConsultationCubit, ConsultationState>(
        listener: (context, state) {
          if (state is ConsultationEnded) {
            Navigator.of(context).pop();
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('تم إنهاء الاستشارة الطبية بنجاح.')),
            );
          }
        },
        builder: (context, state) {
          if (state is ConsultationLoading) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.lightPrimary),
            );
          }

          if (state is ConsultationError) {
            return Center(
              child: Text(state.message, style: const TextStyle(color: Colors.red)),
            );
          }

          if (state is ConsultationActive) {
            return Column(
              children: [
                // Video View Container
                Expanded(
                  child: Stack(
                    children: [
                      Container(
                        margin: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.black87,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Center(
                          child: state.isCameraOn
                              ? Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    const Icon(Icons.person, size: 80, color: Colors.white54),
                                    const SizedBox(height: 12),
                                    Text(
                                      'د. أحمد المحمدي (${state.session.sessionStatus})',
                                      style: const TextStyle(color: Colors.white, fontSize: 16),
                                    ),
                                  ],
                                )
                              : const Text('تم إيقاف الكاميرا', style: TextStyle(color: Colors.white70)),
                        ),
                      ),
                      // Connection Quality Indicator
                      Positioned(
                        top: 28,
                        right: 28,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: Colors.green.withOpacity(0.8),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Row(
                            children: [
                              Icon(Icons.wifi, color: Colors.white, size: 16),
                              SizedBox(width: 6),
                              Text('اتصال ممتاز', style: TextStyle(color: Colors.white, fontSize: 12)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                // Control Bar
                GlassCard(
                  margin: const EdgeInsets.all(16),
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      IconButton(
                        icon: Icon(
                          state.isMuted ? Icons.mic_off : Icons.mic,
                          color: state.isMuted ? Colors.red : AppColors.lightPrimary,
                        ),
                        onPressed: () => context.read<ConsultationCubit>().toggleMute(),
                      ),
                      IconButton(
                        icon: Icon(
                          state.isCameraOn ? Icons.videocam : Icons.videocam_off,
                          color: state.isCameraOn ? AppColors.lightPrimary : Colors.red,
                        ),
                        onPressed: () => context.read<ConsultationCubit>().toggleCamera(),
                      ),
                      FloatingActionButton(
                        backgroundColor: Colors.red,
                        onPressed: () => context.read<ConsultationCubit>().endConsultation(),
                        child: const Icon(Icons.call_end, color: Colors.white),
                      ),
                    ],
                  ),
                ),
              ],
            );
          }

          return const SizedBox.shrink();
        },
      ),
    );
  }
}
